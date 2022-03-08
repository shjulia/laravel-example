<?php

declare(strict_types=1);

namespace App\Mail\Users\Practice;

use App\Entities\User\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

/**
 * Class ContinueHiringMail
 * @package App\Mail\Users\Practice
 */
class ContinueHiringMail extends Mailable implements ShouldQueue
{
    use Queueable;
    use SerializesModels;

    /**
     * @var User
     */
    public $user;
    /**
     * @var string
     */
    public $code;

    /**
     * ContinueHiringMail constructor.
     * @param User $user
     * @param string $code
     */
    public function __construct(User $user, string $code)
    {
        $this->user = $user;
        $this->code = $code;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject("We Miss You! Here's a Small Gift")
            ->view('emails.users.practice.continue-hiring');
    }
}
