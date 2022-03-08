<?php

namespace App\Listeners\Shift;

use App\Entities\Shift\Shift;
use App\Entities\User\Practice\Practice;
use App\Entities\User\Provider\Specialist;
use App\Entities\User\Referral;
use App\Entities\User\User;
use App\Mail\Invite\GotCashForReferralMail;
use App\Repositories\Invite\InviteRepository;
use App\Repositories\User\SpecialistRepository;
use DB;
use App\Events\Shift\PaymentEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\UseCases\Shift\PaymentService;
use App\Services\Notifications\Shifts\NotifyService;
use App\UseCases\Admin\Notifications\PaymentsProblemService;
use Illuminate\Support\Facades\Mail;

/**
 * Class PaymentListener
 * Runs payment flow.
 *
 * Event {@see \App\Events\Shift\PaymentEvent}
 * @package App\Listeners\Shift
 */
class PaymentListener implements ShouldQueue
{
    /** @var PaymentService $paymentService */
    protected $paymentService;

    /** @var NotifyService $notifyService */
    protected $notifyService;

    /** @var PaymentsProblemService $paymentsProblemService */
    protected $paymentsProblemService;

    /** @var SpecialistRepository $specialistRepository */
    private $specialistRepository;

    /** @var InviteRepository */
    private $inviteRepository;

    /**
     * PaymentListener constructor.
     *
     * @param PaymentService $paymentService
     * @param NotifyService $notifyService
     * @param PaymentsProblemService $paymentsProblemService
     * @param SpecialistRepository $specialistRepository
     * @param InviteRepository $inviteRepository
     */
    public function __construct(
        PaymentService $paymentService,
        NotifyService $notifyService,
        PaymentsProblemService $paymentsProblemService,
        SpecialistRepository $specialistRepository,
        InviteRepository $inviteRepository
    ) {
        $this->paymentService = $paymentService;
        $this->notifyService = $notifyService;
        $this->paymentsProblemService = $paymentsProblemService;
        $this->specialistRepository = $specialistRepository;
        $this->inviteRepository = $inviteRepository;
    }

    /**
     * @param PaymentEvent $event
     * @throws \Throwable
     */
    public function handle(PaymentEvent $event)
    {
        /** @var Shift $shift */
        $shift = $event->shift;
        /** @var Specialist $provider */
        $provider = $shift->provider;
        /** @var Practice $practice */
        $practice = $shift->practice;

        $this->paymentService->replenish($provider->user, $shift->cost, 'For shift #' . $shift->id);

        DB::beginTransaction();
        try {
            if ($provider->isExpeditedPaymentStatus()) {
                $this->paymentService->withdraw($provider->user, $shift->cost, true, true);
            }
            $this->specialistRepository->setPaidTotal($provider, $shift->cost, round($shift->shift_time / 60));
            DB::commit();
        } catch (\DomainException $e) {
            DB::rollback();
            $this->paymentsProblemService->notify($provider->user);
            \LogHelper::error($e);
        }
        $this->checkInvite($provider->user);
        $this->checkInvite($shift->creator);
        $this->notifyService->shiftEndPracticeNotification($provider->user, $shift);
        $this->notifyService->shiftEndProviderNotification($practice, $shift);
    }

    /**
     * @param User $user
     */
    private function checkInvite(User $user): void
    {
        $invite = $this->inviteRepository->findInvitedNotPaidUser($user->id);
        if (!$invite) {
            return;
        }
        try {
            $invite->referral()->increment('referral_money_earned', Referral::REFERRAL_FEE);
            $invite->edit($amount = Referral::REFERRAL_FEE);
            $this->paymentService->replenishAndWithDraw(
                $user,
                (float)$amount,
                'For Invitation of user #' . $invite->user_id,
                false,
                false
            );
            $invite->save();
        } catch (\DomainException $e) {
            \Log::error($e->getMessage(), $e->getTrace());
        }
        Mail::to($invite->referral->user)->send(new GotCashForReferralMail($invite));
    }
}
