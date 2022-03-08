<?php

declare(strict_types=1);

namespace App\Listeners\User;

use App\Events\User\AccountRejected;
use App\Notifications\User\AccountRejectedNotification;

/**
 * Class AccountRejectedListener
 * Sends notification to all types of users that their account was rejected.
 *
 * Event {@see \App\Events\User\AccountRejected}
 * @package App\Listeners\User
 */
class AccountRejectedListener
{
    /**
     * @param AccountRejected $event
     */
    public function handle(AccountRejected $event): void
    {
        $user = $event->user;
        $user->notify(new AccountRejectedNotification());
    }
}
