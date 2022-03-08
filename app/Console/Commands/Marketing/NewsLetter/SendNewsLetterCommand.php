<?php

declare(strict_types=1);

namespace App\Console\Commands\Marketing\NewsLetter;

use App\Entities\NewsLetter\NewsLetter;
use App\Entities\User\Role;
use App\Repositories\User\PracticeRepository;
use App\Repositories\User\SpecialistRepository;
use App\UseCases\NewsLetter\NewsLetter\Finish;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendNewsLetterCommand extends Command
{
    protected $signature = 'send:newsletters';
    protected $description = 'Finds and send newsletter';
    /**
     * @var Finish\Handler
     */
    private $finishHandler;
    /**
     * @var SpecialistRepository
     */
    private $providers;
    /**
     * @var PracticeRepository
     */
    private $practices;

    public function __construct(
        Finish\Handler $finishHandler,
        SpecialistRepository $providers,
        PracticeRepository $practices
    ) {
        parent::__construct();
        $this->finishHandler = $finishHandler;
        $this->providers = $providers;
        $this->practices = $practices;
    }

    public function handle()
    {
        try {
            $newsLetters = NewsLetter::where('is_finished', false)
                ->where(
                    'start_date',
                    '<=',
                    (new \DateTimeImmutable())
                        ->setTimezone(new \DateTimeZone('America/New_York'))
                        ->format('Y-m-d H:i:s')
                )
                ->with('template')
                ->get();
            foreach ($newsLetters as $newsLetter) {
                $this->handleNewsLetter($newsLetter);
            }
        } catch (\Throwable $e) {
            \Log::error($e->getMessage());
        }
    }

    private function handleNewsLetter(NewsLetter $newsLetter): void
    {
        $command = new Finish\Command($newsLetter->id);
        $this->finishHandler->handle($command);
        $emails = json_decode($newsLetter->emails, true);
        foreach ($emails as $email) {
            $this->send($email, $newsLetter->template->html_content, $newsLetter->subject);
        }
        /** @var Role|null $role */
        $role = $newsLetter->role;
        if (!$role) {
            return;
        }
        if ($role->type === Role::ROLE_PROVIDER) {
            foreach ($this->providers->findAllPaginate() as $provider) {
                $this->send($provider->user->email, $newsLetter->template->html_content, $newsLetter->subject);
            }
        }
        if ($role->type === Role::ROLE_PRACTICE) {
            foreach ($this->practices->findAllPaginate() as $practice) {
                foreach ($practice->users as $user) {
                    $this->send($user->email, $newsLetter->template->html_content, $newsLetter->subject);
                }
            }
        }
    }

    private function send(string $email, string $html, string $subject): void
    {
        try {
            Mail::send([], [], function ($message) use ($email, $html, $subject) {
                $message->to($email)
                    ->subject($subject)
                    ->setBody($html, 'text/html');
            });
        } catch (\Throwable $e) {
            \Log::error($e->getMessage());
        }
    }
}
