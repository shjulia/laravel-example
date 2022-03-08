<?php

declare(strict_types=1);

namespace App\Mail\Users\Practice;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TempNotEligibleMail extends Mailable implements ShouldQueue
{
    use Queueable;
    use SerializesModels;

    public function build()
    {
        $link = route('shifts.base');
        return $this->subject('Request Temps Confidently via Boon')
            ->view('emails.users.practice.temp-not-eligible')
            ->with('link', $link);
    }
}
