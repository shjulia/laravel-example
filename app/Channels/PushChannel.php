<?php

namespace App\Channels;

use App\Notifications\PushNotification;
use App\Services\OneSignal\CreateNotificationService;

class PushChannel
{
    /** @var CreateNotificationService $notificationService */
    private $notificationService;

    public function __construct(CreateNotificationService $createNotificationService)
    {
        $this->notificationService = $createNotificationService;
    }

    public function send($notifiable, PushNotification $notification)
    {
        $playerIds = $notifiable->players->pluck('player_id')->toArray();
        if (!empty($playerIds)) {
            $this->notificationService->sendMessage($playerIds, $notification->getData());
        }
    }
}
