<?php

namespace App\Mail\Signup;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

/**
 * Class WelcomeMail
 * @package App\Mail\Signup
 */
class WelcomeMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    /**
     * @var string
     */
    protected $setPasswordLink;

    /**
     * WelcomeMail constructor.
     * @param string $setPasswordLink
     */
    public function __construct(string $setPasswordLink)
    {
        $this->setPasswordLink = $setPasswordLink;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.signup.welcome')
            ->text('emails.signup.welcome-text')
            ->with([
            'setPasswordLink' => $this->setPasswordLink
        ]);
    }
}
