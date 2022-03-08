<?php

declare(strict_types=1);

namespace App\Listeners\User;

use App\Events\User\LoginEvent;
use App\UseCases\Auth\LoginLogService;
use Illuminate\Contracts\Queue\ShouldQueue;

/**
 * Class LoginListener
 * Writes down when user logged in.
 *
 * Event {@see \App\Events\User\LoginEvent}
 * @package App\Listeners\User
 */
class LoginListener implements ShouldQueue
{
    /**
     * @var LoginLogService
     */
    private $logService;

    /**
     * LoginListener constructor.
     * @param LoginLogService $logService
     */
    public function __construct(LoginLogService $logService)
    {
        $this->logService = $logService;
    }

    /**
     * @param LoginEvent $event
     */
    public function handle(LoginEvent $event): void
    {
        $this->logService->log($event->user);
    }
}
