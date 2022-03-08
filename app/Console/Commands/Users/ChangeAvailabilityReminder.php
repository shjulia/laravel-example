<?php

declare(strict_types=1);

namespace App\Console\Commands\Users;

use App\Entities\DTO\SmsDTO;
use App\Events\Shift\NotifyNotAvailableProviders;
use App\Mail\Shift\AvailabelMail;
use App\Notifications\SmsNotification;
use App\Repositories\User\SpecialistRepository;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

/**
 * Class ChangeAvailabilityReminder
 * Reminds providers to change availability settings.
 *
 * @package App\Console\Commands\Users
 */
class ChangeAvailabilityReminder extends Command
{
    /**
     * @var string
     */
    protected $signature = 'user:availability';

    /**
     * @var string
     */
    protected $description = 'Change availability reminder for providers';
    /**
     * @var SpecialistRepository
     */
    private $specialistRepository;


    public function __construct(SpecialistRepository $specialistRepository)
    {
        $this->specialistRepository = $specialistRepository;
        parent::__construct();
    }

    public function handle()
    {
        $providers = $this->specialistRepository->getNotAvailable();
        foreach ($providers as $provider) {
            try {
                Mail::to($provider->user->email)->send(new AvailabelMail($provider->user->full_name));
                if ($provider->user->phone) {
                    $provider->user->notify(new SmsNotification(new SmsDTO(
                        "Shifts are waiting for you. Change availability: ",
                        route('shifts.provider.index')
                    )));
                }
            } catch (\Exception $e) {
                \LogHelper::error($e, ['message' => 'ChangeAvailabilityReminder', 'provider' => $provider->id]);
            }
        }
    }
}
