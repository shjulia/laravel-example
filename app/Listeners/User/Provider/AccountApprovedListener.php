<?php

namespace App\Listeners\User\Provider;

use App\Entities\DTO\Notification;
use App\Events\User\Provider\AccountApproved;
use App\Notifications\GlobalNotification;
use App\Notifications\PushNotification;
use App\Notifications\User\Provider\AccountApprovedNotification;
use App\Notifications\WebPushNotification;
use Illuminate\Contracts\Notifications\Dispatcher as Notificator;

/**
 * Class AccountApprovedListener
 * Sends notifications to providers about account approval.
 *
 * Event {@see \App\Events\User\Provider\AccountApproved}
 * @package App\Listeners\User
 */
class AccountApprovedListener
{
    /**
     * @var Notificator
     */
    private $notificator;

    /**
     * AccountApprovedListener constructor.
     * @param Notificator $notificator
     */
    public function __construct(Notificator $notificator)
    {
        $this->notificator = $notificator;
    }

    /**
     * @param AccountApproved $event
     */
    public function handle(AccountApproved $event): void
    {
        $user = $event->user;
        $this->notificator->send($user, new AccountApprovedNotification());
        $notification = new Notification('Your Account is approved', $user->id, null, null, route('home'), 'fa-check');
        $this->notificator->send($user, new GlobalNotification($notification));
        $this->notificator->send($user, new WebPushNotification($notification));
        $this->notificator->send($user, new PushNotification($notification));
    }
}
