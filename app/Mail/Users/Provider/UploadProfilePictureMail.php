<?php

declare(strict_types=1);

namespace App\Mail\Users\Provider;

use Illuminate\Mail\Mailable;

class UploadProfilePictureMail extends Mailable
{
    public function build()
    {
        $link = route('provider.edit.identity');
        return $this->subject('We Can\'t See Your Beautiful Face')
            ->view('emails.users.provider.upload-profile-picture')
            ->with('link', $link);
    }
}
