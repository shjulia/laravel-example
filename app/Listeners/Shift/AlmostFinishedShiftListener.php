<?php

declare(strict_types=1);

namespace App\Listeners\Shift;

use App\Entities\DTO\Notification as NotificationDTO;
use App\Entities\DTO\SmsDTO;
use App\Entities\Shift\Shift;
use App\Events\Shift\AlmostFinishedShiftEvent;
use App\Mail\Shift\Finish\FinishShiftPractice;
use App\Mail\Shift\Finish\FinishShiftProvider;
use App\Notifications\PushNotification;
use App\Notifications\SmsNotification;
use App\Notifications\WebPushNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

/**
 * Class AlmostFinishedShiftListener
 *
 * Event {@see \App\Events\Shift\AlmostFinishedShiftEvent}
 * @package App\Listeners\Shift
 */
class AlmostFinishedShiftListener implements ShouldQueue
{
    /**
     * @param AlmostFinishedShiftEvent $event
     */
    public function handle(AlmostFinishedShiftEvent $event): void
    {
        /** @var Shift $shift */
        $shift = $event->shift;
        $shift->update([
           'notified_finish' => 1
        ]);
        if ($shift->multi_days) {
            return;
        }

        //Mail::to($shift->creator->email)->send(new FinishShiftPractice($shift));
        //Mail::to($shift->provider->user->email)->send(new FinishShiftProvider($shift));
        $text = 'Your shift is almost over! Please be sure to click the “End Shift” button on your Boon app when the ' .
            'work is complete!';

        $practiceUser = $shift->creator;
        $practiceUser->notify(new SmsNotification(new SmsDTO($text, route('shifts.details', $shift))));
        $providerUser = $shift->provider->user;
        $providerUser->notify(new SmsNotification(new SmsDTO($text, route('shifts.provider.info', $shift))));

        $notification = new NotificationDTO(
            $text,
            $providerUser->id,
            null,
            null,
            route('shifts.provider.info', $shift),
            'fa-commenting-o'
        );
        $providerUser->notify(new WebPushNotification($notification));
        $providerUser->notify(new PushNotification($notification));

        $notification = new NotificationDTO(
            $text,
            $practiceUser->id,
            null,
            null,
            route('shifts.details', $shift)
        );
        $practiceUser->notify(new WebPushNotification($notification));
        $practiceUser->notify(new PushNotification($notification));
    }
}
