<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushChannel;
use NotificationChannels\WebPush\WebPushMessage;

class WebPushNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /** @var \App\Entities\DTO\Notification $notificationData */
    private $notificationData;

    /**
     * GlobalNotification constructor.
     * @param \App\Entities\DTO\Notification $notification
     */
    public function __construct(\App\Entities\DTO\Notification $notification)
    {
        $this->notificationData = $notification;
    }

    public function via($notifiable)
    {
        return [WebPushChannel::class];
    }

    public function toWebPush($notifiable, $notification)
    {
        $data = $this->notificationData;
        return (new WebPushMessage())
            ->title('Message From Boon!')
            ->icon(asset('img/icons/Logo_48x48.png'))
            ->badge(asset('img/icons/Logo_48x48.png'))
            ->body($data->title)
            ->action('Details', $data->link ?: route('home'));
        // ->data(['id' => $notification->id])
        // ->badge()
        // ->dir()
        // ->image()
        // ->lang()
        // ->renotify()
        // ->requireInteraction()
        // ->tag()
        // ->vibrate()
    }
}
