<?php

namespace App\Mail\Shift;

use Illuminate\Mail\Mailable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Queue\SerializesModels;

/**
 * Class AvailabelMail
 * @package App\Mail\Shift
 */
class AvailabelMail extends Mailable
{
    use SerializesModels;

    /**
     * @var string
     */
    public $name;

    /**
     * AvailabelMail constructor.
     * @param string $name
     */
    public function __construct(string $name)
    {
        $this->name = $name;
    }

    /**
     * @return AvailabelMail
     */
    public function build()
    {
        return $this->subject('Shifts are waiting for you')
            ->view('emails.shift.change-availability')
            ->text('emails.shift.text.change-availability-text')
            ->with(['name' => $this->name]);
    }
}
