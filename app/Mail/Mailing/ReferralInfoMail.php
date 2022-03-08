<?php

declare(strict_types=1);

namespace App\Mail\Mailing;

use App\Entities\User\User;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

/**
 * Class ReferralInfoMail
 * @package App\Mail\Mailing
 */
class ReferralInfoMail extends Mailable
{
    use SerializesModels;

    /**
     * @var User
     */
    private $user;

    /**
     * ReferralInfoMail constructor.
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * @return ReferralInfoMail
     */
    public function build()
    {
        return $this->subject('Get $100, Tell a Friend about Boon')
            ->view('emails.mailing.referral-info')
            ->with(['user' => $this->user]);
    }
}
