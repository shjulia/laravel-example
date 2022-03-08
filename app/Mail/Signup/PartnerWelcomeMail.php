<?php

namespace App\Mail\Signup;

use App\Entities\User\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class PartnerWelcomeMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    /**
     * @var User
     */
    private $user;

    /**
     * PartnerWelcomeMail constructor.
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $link = route('signup.userBaseByInvite', ['code' => $this->user->referral->referral_code]);
        return $this->view('emails.signup.welcome-partner', compact('link'))
            ->text('emails.signup.welcome-partner-text', compact('link'));
    }
}
