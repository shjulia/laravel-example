<?php

declare(strict_types=1);

namespace App\Mail\Invite;

use App\Entities\Invite\Invite;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

/**
 * Class GotCashForReferralMail
 * @package App\Mail\Invite
 */
class GotCashForReferralMail extends Mailable implements ShouldQueue
{
    use Queueable;
    use SerializesModels;

    /**
     * @var Invite
     */
    public $invite;

    /**
     * InviteMail constructor.
     * @param Invite $invite
     */
    public function __construct(Invite $invite)
    {
        $this->invite = $invite;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('You\'ve Got Cash')
            ->view('emails.referral.got-cash');
    }
}
