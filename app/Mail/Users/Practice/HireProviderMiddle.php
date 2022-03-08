<?php

declare(strict_types=1);

namespace App\Mail\Users\Practice;

use App\Entities\User\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

/**
 * Class HireProviderMiddle
 * @package App\Mail\Users\Practice
 */
class HireProviderMiddle extends Mailable implements ShouldQueue
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
     * HireProviderMiddle constructor.
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
        return $this->subject("We Will Meet You in the Middle. Here's 50% off Your Next Temp")
            ->view('emails.users.practice.hire-middle');
    }
}
