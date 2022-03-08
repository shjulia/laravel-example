<?php

namespace App\Repositories\User;

use App\Entities\User\Referral;

/**
 * Class ReferralRepository
 * @package App\Repositories\User
 */
class ReferralRepository
{
    /**
     * @param string $code
     * @return Referral|null
     */
    public function findByCode(string $code): ?Referral
    {
        return Referral::where('referral_code', $code)->first();
    }
}
