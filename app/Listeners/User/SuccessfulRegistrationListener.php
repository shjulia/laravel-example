<?php

namespace App\Listeners\User;

use App\Entities\DTO\Notification;
use App\Events\User\SuccessfulRegistrationEvent;
use App\Notifications\GlobalNotification;

/**
 * Class SuccessfulRegistrationListener
 * Sends welcome notification and notification which reminds to add additional information to user account.
 *
 * Event {@see \App\Events\User\SuccessfulRegistrationEvent}
 * @package App\Listeners\User
 */
class SuccessfulRegistrationListener
{
    /**
     * @param SuccessfulRegistrationEvent $event
     */
    public function handle(SuccessfulRegistrationEvent $event): void
    {
        $user = $event->user;

        $notificationWelcome = new Notification('Welcome to boon!', $user->id);
        \Notification::send($user, new GlobalNotification($notificationWelcome));

        $notificationInfo = new Notification(
            'Add additional information to your account',
            $user->id,
            null,
            null,
            $event->link,
            'fa-user'
        );
        \Notification::send($user, new GlobalNotification($notificationInfo));
    }
}
