<?php

declare(strict_types=1);

namespace App\Services\Notifications\Referrals;

use App\Mail\Shift\ReferredFriend;

/**
 * Class NotifyService
 * @package App\Services\Notifications\Referrals
 */
class NotifyService
{
    /**
     * notify the referral of the end of the shift
     * and receiving referral money
     *
     * @param $referral
     * @param $provider
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function shiftEndReferralNotification($referral, $provider)
    {
        try {
            \Mail::to($referral)->send(new ReferredFriend($provider));
        } catch (\Exception $exception) {
            \LogHelper::error($exception, ['userId' => $referral->user_id]);
            return response($exception->getMessage(), 500);
        }

        return response('Email sent successfully', 200);
    }
}
