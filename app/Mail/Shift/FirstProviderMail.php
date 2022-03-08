<?php

declare(strict_types=1);

namespace App\Mail\Shift;

use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class FirstProviderMail extends Mailable
{
    use SerializesModels;

    public function build()
    {
        return $this->subject('Congrats on Requesting Your first Provider')
            ->view('emails.shift.first-provider')
            ->with(['link' => 'https://www.doingboon.com/faq/']);
    }
}
