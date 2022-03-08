<?php

namespace App\Mail\Invite;

use App\Entities\Invite\Invite;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Queue\SerializesModels;

/**
 * Class InviteMail
 * @package App\Mail\Invite
 */
class InviteMail extends Mailable
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
        $invite = $this->invite;
        return $this->subject('You Have Been Invited to Boon by ' . $invite->referral->user->full_name)
            ->view('emails.signup.invite')
            ->text('emails.signup.invite-text')
            ->with([
                'referralFullName' => $invite->referral->user->full_name,
                'referralCode' => $invite->referral->referral_code
            ]);
        /*return $this->markdown('vendor.notifications.email')->with([
            "level" => "default",
            "greeting" => "Hi, " . $invite->email,
            "introLines" => [
                "You have been invited to boon by " . $invite->referral->user->full_name
            ],
            "actionText" => "Follow invite",
            "actionUrl" => route('signup.userBaseByInvite', ['code' => $invite->referral->referral_code]),
            "outroLines" => [""]
        ]);*/
    }
}
