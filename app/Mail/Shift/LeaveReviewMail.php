<?php

namespace App\Mail\Shift;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class LeaveReviewMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    /** @var string */
    public $userName;

    /** @var string */
    public $coworkerName;

    /** @var string */
    public $link;

    /**
     * InviteMail constructor.
     * @param string $userName
     * @param string $coworkerName
     * @param string $link
     */
    public function __construct(string $userName, string $coworkerName, string $link)
    {
        $this->userName = $userName;
        $this->coworkerName = $coworkerName;
        $this->link = $link;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Leave Review')
            ->view('emails.shift.leave-review')
            ->text('emails.shift.text.leave-review-text')
            ->with([
                'coworkerName' => $this->coworkerName,
                'link' => $this->link
            ]);
    }
}
