<?php

namespace App\Channels;

use App\Notifications\GlobalNotification;
use Illuminate\Notifications\Notification;
use App\Entities\Notification\Notification as NotificationEntity;

class CustomDBChannel
{
    /**
     * @param $notifiable
     * @param GlobalNotification $notification
     */
    public function send($notifiable, GlobalNotification $notification)
    {
        $notificationEntity = NotificationEntity::create($notification->toDatabase($notifiable)->getArray());

        if ($notification->eventClass && $notification->broadcastChanel) {
            $class = $notification->eventClass;
            event(new $class($notifiable, $notificationEntity, $notification->broadcastChanel));
        }
    }
}
