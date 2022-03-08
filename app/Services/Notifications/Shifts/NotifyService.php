<?php

declare(strict_types=1);

namespace App\Services\Notifications\Shifts;

use App\Entities\Shift\Shift;
use App\Entities\User\Practice\Practice;
use App\Entities\User\User;
use App\Mail\Shift\ReferredFriend;
use App\Mail\Shift\ShiftFinished;
use App\Mail\Shift\ShiftFinishedProvider;
use Illuminate\Contracts\Mail\Mailer;

/**
 * Class NotifyService
 * Notifies practice, provider and referral about shift end.
 *
 * @package App\Services\Notifications\Shifts
 */
class NotifyService
{
    /**
     * @var Mailer
     */
    private $mailer;

    public function __construct(Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    /**
     * @param User $provider
     * @param Shift $shift
     */
    public function shiftEndPracticeNotification(User $provider, Shift $shift)
    {
        try {
            $this->mailer->to($shift->creator)->send(new ShiftFinished($provider, $shift));
        } catch (\Exception $e) {
            \LogHelper::error($e);
        }
    }

    /**
     * @param Practice $practice
     * @param Shift $shift
     */
    public function shiftEndProviderNotification(Practice $practice, Shift $shift)
    {
        try {
            $this->mailer->to($shift->provider->user)->send(new ShiftFinishedProvider($practice, $shift));
        } catch (\Exception $e) {
            \LogHelper::error($e, ['userId' => $shift->creator->id]);
        }
    }

    /**
     * @param $user_id
     */
    public function shiftEndReferralNotification($user_id)
    {
        $user = User::find($user_id);

        $referral_name = $user->first_name . ' ' . $user->last_name;

        try {
            $this->mailer->to($user)->send(new ReferredFriend($referral_name));
        } catch (\Exception $e) {
            \LogHelper::error($e, ['userId' => $user->id]);
        }
    }
}
