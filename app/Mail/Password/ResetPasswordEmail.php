<?php

namespace App\Mail\Password;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class ResetPasswordEmail extends Mailable
{
    use Queueable;
    use SerializesModels;

    protected $token;

    /**
     * Create a new message instance.
     *
     * @param $token
     * @return void
     */
    public function __construct($token)
    {
        $this->token = $token;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.password.reset-password')
            ->text('emails.password.reset-password-text')
            ->with(['token' => $this->token]);
    }
}
