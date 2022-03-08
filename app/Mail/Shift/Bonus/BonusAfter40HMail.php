<?php

declare(strict_types=1);

namespace App\Mail\Shift\Bonus;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

/**
 * Class BonusAfter40HMail
 * @package App\Mail\Shift\Bonus
 */
class BonusAfter40HMail extends Mailable implements ShouldQueue
{
    use Queueable;
    use SerializesModels;

    /**
     * @return BonusAfter40HMail
     */
    public function build()
    {
        return $this->subject('You have worked more than 40 hours via Boon!')
            ->view('emails.shift.bonus.40h');
    }
}
