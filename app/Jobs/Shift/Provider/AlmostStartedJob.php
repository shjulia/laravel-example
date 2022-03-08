<?php

declare(strict_types=1);

namespace App\Jobs\Shift\Provider;

use App\Entities\DTO\Notification as NotificationDTO;
use App\Entities\DTO\SmsDTO;
use App\Entities\Shift\Shift;
use App\Notifications\PushNotification;
use App\Notifications\SmsNotification;
use App\Notifications\WebPushNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Class AlmostStartedJob
 * @package App\Jobs\Shift\Provider
 */
class AlmostStartedJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * @var Shift
     */
    private $shift;

    /**
     * BonusAddedJob constructor.
     * @param Shift $shift
     */
    public function __construct(Shift $shift)
    {
        $this->shift = $shift;
    }

    public function handle()
    {
        $provider = $this->shift->provider;
        $user = $provider->user;
        $link = route('shifts.provider.info', ['shift' => $this->shift]);
        $notification = new NotificationDTO(
            "Be sure to check-in to start your shift when you arrive.",
            $user->id,
            null,
            null,
            $link,
            'fa-commenting-o'
        );

        $user->notify(new SmsNotification(new SmsDTO($notification->title, $notification->link)));
        $user->notify(new WebPushNotification($notification));
        $user->notify(new PushNotification($notification));
    }
}
