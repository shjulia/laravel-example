<?php

declare(strict_types=1);

namespace App\Mail\Invite;

use App\Entities\Invite\Invite;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

/**
 * Class ReferralMissing
 * @package App\Mail\Invite
 */
class ReferralMissingMail extends Mailable implements ShouldQueue
{
    use Queueable;
    use SerializesModels;

    /**
     * @var Invite[]|Collection
     */
    public $invites;

    /**
     * ReferralMissing constructor.
     * @param Invite[]|Collection $invites
     */
    public function __construct($invites)
    {
        $this->invites = $invites;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('You\'re Missing Out on $100+')
            ->view('emails.referral.missing');
    }
}
