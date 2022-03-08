<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Channels\SmsChannel;
use App\Entities\DTO\SmsDTO;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

/**
 * Class SmsNotification
 * @package App\Notifications
 */
class SmsNotification extends Notification //implements ShouldQueue
{
    //use Queueable;

    /**
     * @var SmsDTO
     */
    private $notificationData;

    /**
     * SmsNotification constructor.
     * @param SmsDTO $notificationData
     */
    public function __construct(SmsDTO $notificationData)
    {
        $this->notificationData = $notificationData;
    }

    /**
     * @param $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return [SmsChannel::class];
    }

    /**
     * @return SmsDTO
     */
    public function getData()
    {
        return $this->notificationData;
    }
}
