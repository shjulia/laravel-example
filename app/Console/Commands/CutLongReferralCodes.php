<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Entities\User\Referral;
use Illuminate\Console\Command;

/**
 * Class CutLongReferralCodes
 * Cut Long Referral Codes. At the beginning referral codes was too long.
 * @package App\Console\Commands
 */
class CutLongReferralCodes extends Command
{
    /**
     * @var string
     */
    protected $signature = 'referral:cut';

    /**
     * @var string
     */
    protected $description = 'Cut Long Referral Codes';

    public function handle()
    {
        $codes = Referral::get();
        foreach ($codes as $code) {
            if (strlen($code->referral_code) < 20) {
                continue;
            }

            $newCode = substr($code->referral_code, 0, 5);
            $code->update(['referral_code' => $newCode]);
        }
    }
}
