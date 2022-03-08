<?php

namespace App\Notifications;

use App\Channels\CustomDBChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use App\Entities\DTO\Notification as NotificationDTO;

/**
 * Class GlobalNotification
 * @package App\Notifications
 */
class GlobalNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * @var NotificationDTO
     */
    private $notificationData;

    /**
     * @var null|string
     */
    public $eventClass;

    /**
     * @var null|string
     */
    public $broadcastChanel;


    /**
     * GlobalNotification constructor.
     * @param NotificationDTO $notificationData
     * @param null|string $eventClass
     * @param null|string $broadcastChanel
     */
    public function __construct(
        NotificationDTO $notificationData,
        ?string $eventClass = null,
        ?string $broadcastChanel = null
    ) {
        $this->notificationData = $notificationData;
        $this->eventClass = $eventClass;
        $this->broadcastChanel = $broadcastChanel;
    }

    /**
     * @param $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return [CustomDBChannel::class];
    }

    public function toDatabase($notifiable)
    {
        return $this->notificationData;
    }
}
