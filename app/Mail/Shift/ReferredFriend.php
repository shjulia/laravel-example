<?php

namespace App\Mail\Shift;

use App\Entities\User\Referral;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class ReferredFriend extends Mailable implements ShouldQueue
{
    use Queueable;
    use SerializesModels;

    public $referral_name;
    public $referral_fee;

    /**
     * Create a new message instance.
     *
     * @param $referral_name
     * @return void
     */
    public function __construct($referral_name)
    {
        $this->referral_name = $referral_name;
        $this->referral_fee  = Referral::REFERRAL_FEE;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.shift.referred-friend')
            ->text('emails.shift.text.referred-friend-text')
            ->with([
                'referral_name' => $this->referral_name,
                'referral_fee' => $this->referral_fee
            ]);
    }
}
