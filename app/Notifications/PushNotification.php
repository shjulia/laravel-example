<?php

namespace App\Notifications;

use App\Channels\PushChannel;
use App\Entities\DTO\Notification as NotificationDTO;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class PushNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * @var NotificationDTO
     */
    private $notificationData;

    /**
     * GlobalNotification constructor.
     * @param NotificationDTO $notificationData
     */
    public function __construct(NotificationDTO $notificationData)
    {
        $this->notificationData = $notificationData;
    }

    /**
     * @param $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return [PushChannel::class];
    }

    public function getData()
    {
        return $this->notificationData;
    }
}
