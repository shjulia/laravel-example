<?php

namespace App\Listeners\Shift;

use App\Entities\DTO\Notification as NotificationDTO;
use App\Entities\DTO\SmsDTO;
use App\Entities\Shift\Shift;
use App\Entities\User\User;
use App\Events\Shift\AcceptShiftEvent;
use App\Events\Shift\NotifyShiftEvent;
use App\Jobs\Shift\InviteByTextJob;
use App\Notifications\GlobalNotification;
use App\Notifications\PushNotification;
use App\Notifications\SmsNotification;
use App\Notifications\WebPushNotification;
use App\Notifications\Shift\AcceptShiftNotification;
use App\UseCases\Emails\Provider\ProfilePictureReminderService;
use Illuminate\Contracts\Queue\ShouldQueue;

/**
 * Class AcceptShiftListener
 *
 * Event {@see \App\Events\Shift\AcceptShiftEvent}
 * @package App\Listeners\Shift
 */
class AcceptShiftListener implements ShouldQueue
{
    /**
     * @var ProfilePictureReminderService
     */
    private $pictureReminderService;

    /**
     * AcceptShiftListener constructor.
     * @param ProfilePictureReminderService $pictureReminderService
     */
    public function __construct(ProfilePictureReminderService $pictureReminderService)
    {
        $this->pictureReminderService = $pictureReminderService;
    }

    /**
     * @param AcceptShiftEvent $event
     */
    public function handle(AcceptShiftEvent $event): void
    {
        /** @var User $user */
        $user = $event->user;
        /** @var Shift $shift */
        $shift = $event->shift;
        $user->notify(new AcceptShiftNotification($shift));
        if ($shift->isShouldSendText()) {
            InviteByTextJob::dispatch($shift, $user);
        } else {
            InviteByTextJob::dispatch($shift, $user)->delay($shift->delayToSendText());
        }

        //notification for provider
        $notification = new NotificationDTO(
            "You've been invited for a job",
            $user->id,
            null,
            null,
            route('shifts.provider.acceptPage', $shift->id),
            'fa-briefcase'
        );
        \Notification::send(
            $user,
            new GlobalNotification($notification, NotifyShiftEvent::class, 'job-chanel.')
        );
        \Notification::send($user, new WebPushNotification($notification));
        \Notification::send($user, new PushNotification($notification));

        if (!$user->provider->hasPhoto()) {
            $this->pictureReminderService->remindUploadPicture($user);
        }
    }
}
