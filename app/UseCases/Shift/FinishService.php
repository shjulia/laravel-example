<?php

declare(strict_types=1);

namespace App\UseCases\Shift;

use App\Entities\DTO\Notification as NotificationDTO;
use App\Entities\Shift\Shift;
use App\Entities\User\Practice\Practice;
use App\Entities\User\Provider\Specialist;
use App\Entities\User\User;
use App\Events\Shift\PaymentEvent;
use App\Jobs\Shift\Provider\CheckDistanceJob;
use App\Mail\Shift\LeaveReviewMail;
use App\Notifications\GlobalNotification;
use App\Notifications\PushNotification;
use App\Notifications\WebPushNotification;
use App\Repositories\Shift\ShiftRepository;
use App\Repositories\User\PracticeRepository;
use Exception;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Contracts\Mail\Mailer;
use Illuminate\Contracts\Notifications\Dispatcher as NotificationsInterface;

/**
 * Class FinishService
 * Finishes Shift, gets charge and sends all related notifications.
 *
 * @package App\UseCases\Shift
 */
class FinishService
{
    /**
     * @var NotificationsInterface
     */
    private $notifier;

    /**
     * @var Mailer
     */
    private $mailer;
    /**
     * @var Dispatcher
     */
    private $dispatcher;
    /**
     * @var PracticeRepository
     */
    private $practiceRepository;
    /**
     * @var ShiftRepository
     */
    private $shiftRepository;
    /**
     * @var ShiftPaymentService
     */
    private $shiftPaymentService;

    public function __construct(
        ShiftPaymentService $shiftPaymentService,
        ShiftRepository $shiftRepository,
        PracticeRepository $practiceRepository,
        NotificationsInterface $notifier,
        Dispatcher $dispatcher,
        Mailer $mailer
    ) {
        $this->notifier = $notifier;
        $this->mailer = $mailer;
        $this->dispatcher = $dispatcher;
        $this->practiceRepository = $practiceRepository;
        $this->shiftRepository = $shiftRepository;
        $this->shiftPaymentService = $shiftPaymentService;
    }

    /**
     * @param Shift $shift
     * @throws Exception
     */
    public function finish(Shift $shift)
    {
        if ($shift->multi_days) { // it will be after all children shifts are finished
            $shift->update(['processed' => 1]);
            $this->recalculatePaymentsValues($shift);
            return;
        }
        $this->capturePracticeCharge($shift);

        /** @var Practice $practice */
        $practice = $shift->practice;
        /** @var Specialist $provider */
        $provider = $shift->provider;
        /** @var User $userPractice */
        $userPractice = $shift->creator;
        /** @var User $userProvider */
        $userProvider = $provider->user;

        if (!$shift->isHasReviewFromPractice()) {
            $link = route('shifts.reviews.review', $shift->id);
            $notification = new NotificationDTO(
                "We would love to hear what you thought of $userProvider->full_name. Please rate them now!",
                $userPractice->id,
                null,
                null,
                $link,
                'fa-commenting-o'
            );

            $this->sendNotifications(
                $notification,
                $userPractice,
                $practice->practice_name,
                $userProvider->full_name,
                $link
            );
        }

        if (!$shift->isHasReviewFromProvider()) {
            $link = route('shifts.provider.reviews.review', $shift->id);
            $notification = new NotificationDTO(
                "We would love to hear what you thought of $practice->practice_name. Please rate them now!",
                $userProvider->id,
                null,
                null,
                $link,
                'fa-commenting-o'
            );

            $this->sendNotifications(
                $notification,
                $userProvider,
                $userProvider->full_name,
                $practice->practice_name,
                $link
            );
        }

        $this->dispatcher->dispatch(new PaymentEvent($shift));
        CheckDistanceJob::dispatch($shift);
        $shift->update(['processed' => 1]);
    }

    /**
     * @param Shift $shift
     * @throws Exception
     */
    private function capturePracticeCharge(Shift $shift): void
    {
        $this->shiftPaymentService->captureCharge($shift);
        $this->practiceRepository->setPaidTotal($shift->practice, $shift->cost_for_practice);
    }

    /**
     * @param Shift $shift
     */
    private function recalculatePaymentsValues(Shift $shift): void
    {
        $cost = 0;
        $costForPractice = 0;
        /** @var Shift $child */
        foreach ($shift->children as $child) {
            if (!$child->isAcceptedByProviderStatus() && !$child->isCompleted() && !$child->isFinishedStatus()) {
                continue;
            }
            $costForPractice += $child->cost_for_practice;
            $cost += $child->cost;
        }
        $shift->editCosts($cost, $costForPractice);
        $this->shiftRepository->save($shift);
    }

    /**
     * @param NotificationDTO $notification
     * @param User $user
     * @param string $nameUser
     * @param string $nameCoworker
     * @param string $link
     */
    public function sendNotifications(
        NotificationDTO $notification,
        User $user,
        string $nameUser,
        string $nameCoworker,
        string $link
    ): void {
        $this->notifier->send($user, new GlobalNotification($notification));
        $this->notifier->send($user, new WebPushNotification($notification));
        $this->notifier->send($user, new PushNotification($notification));
        $this->mailer->to($user->email)->send(new LeaveReviewMail($nameUser, $nameCoworker, $link));
    }
}
