<?php

declare(strict_types=1);

namespace App\Helpers;

use App\Entities\User\Referral;

/**
 * Class ReferralCodeGenerator
 * @package App\Helpers
 */
class ReferralCodeGenerator
{
    public static function generate()
    {
        $code = '';
        $iterator = 0;
        while (true) {
            $code = strtolower(str_random(5));
            if (!Referral::where('referral_code', $code)->first()) {
                break;
            }
            if ($iterator == 20) {
                $code = strtolower(str_random(7));
                break;
            }
            $iterator++;
        }

        return $code;
    }
}
