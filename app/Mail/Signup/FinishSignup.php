<?php

namespace App\Mail\Signup;

use App\Entities\User\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class FinishSignup extends Mailable
{
    use Queueable;
    use SerializesModels;

    /**
     * @var User
     */
    public $user;

    /**
     * @var string|null
     */
    public $subject;

    /**
     * FinishSignup constructor.
     *
     * @param User $user
     * @param string|null $subject
     */
    public function __construct(User $user, ?string $subject = null)
    {
        $this->user = $user;
        $this->subject = $subject;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject($this->subject ?: 'Finish Signup')->view('emails.signup.finish-signup')
            ->text('emails.signup.finish-signup-text')
            ->with([
            'user' => $this->user
        ]);
    }
}
