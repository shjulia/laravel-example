<?php

namespace App\Mail\Admin;

use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

/**
 * Class ErrorMail
 * @package App\Mail\Admin
 */
class ErrorMail extends Mailable
{
    use SerializesModels;

    /**
     * @var string
     */
    public $message;

    /**
     * ErrorMail constructor.
     * @param string $message
     */
    public function __construct(string $message)
    {
        $this->message = $message;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Error')->html($this->message);
    }
}
