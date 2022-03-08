<?php

namespace App\Listeners\Shift\Provider;

use App\Entities\DTO\Notification as NotificationDTO;
use App\Entities\Shift\Shift;
use App\Entities\User\User;
use App\Events\Shift\MatchedEvent;
use App\Events\Shift\NotifyShiftEvent;
use App\Events\Shift\Provider\AcceptShiftEvent;
use App\Notifications\GlobalNotification;
use App\Notifications\PushNotification;
use App\Notifications\WebPushNotification;
use App\Notifications\Shift\Provider\AcceptShiftNotification;

/**
 * Class AcceptShiftListener
 *
 * Event {@see \App\Events\Shift\Provider\AcceptShiftEvent}
 * @package App\Listeners\Shift\Provider
 */
class AcceptShiftListener
{
    /**
     * @param AcceptShiftEvent $event
     */
    public function handle(AcceptShiftEvent $event): void
    {
        $shift = Shift::where('id', $event->shiftId)->first();
        /** @var User $user */
        $user = $shift->creator;
        $providerName = $event->provider->user->first_name . ' ' . $event->provider->user->last_name;
        event(new MatchedEvent($shift->practice_id, $event->provider, $shift->id, $shift->creator_id));
        // add email notification
        $user->notify(new AcceptShiftNotification($shift->id, $providerName));
        $notification = new NotificationDTO(
            "Congratulations! $providerName is on the way!",
            $user->id,
            null,
            null,
            route('shifts.details', $event->shiftId),
            'fa-check-circle-o'
        );
        \Notification::send(
            $user,
            new GlobalNotification($notification, NotifyShiftEvent::class, 'job-chanel.provider-notification.')
        );
        \Notification::send($user, new WebPushNotification($notification));
        \Notification::send($user, new PushNotification($notification));
    }
}
