<?php

declare(strict_types=1);

namespace App\Mail\Shift;

use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class HowPaymentWorksMail extends Mailable
{
    use SerializesModels;

    public function build()
    {
        return $this->subject("You're on your way to earning extra cash!")
            ->view('emails.shift.how-payment-works')
            ->with(['link' => route('provider.edit.getPaid')]);
    }
}
