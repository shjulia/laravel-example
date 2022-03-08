<?php

declare(strict_types=1);

namespace App\UseCases\NewsLetter\NewsLetter\Create;

use App\Entities\NewsLetter\NewsLetter;
use App\Entities\NewsLetter\Template;
use App\Repositories\User\RolesRepository;

class Handler
{
    /**
     * @var RolesRepository
     */
    private $roles;

    public function __construct(RolesRepository $roles)
    {
        $this->roles = $roles;
    }

    public function handle(Command $command): void
    {
        /** @var Template $template */
        $template = Template::where('id', $command->template)->first();
        $newsLetter = NewsLetter::create(
            $template,
            $command->subject,
            \DateTimeImmutable::createFromFormat('Y-m-d\TH:i', $command->start_date),
            'America/New_York',
            $command->role ? $this->roles->getById($command->role) : null
        );
        foreach ($command->emails as $email) {
            $newsLetter->attachEmail($email);
        }
        $newsLetter->saveOrFail();
    }
}
