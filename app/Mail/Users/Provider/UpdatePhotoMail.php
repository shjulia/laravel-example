<?php

declare(strict_types=1);

namespace App\Mail\Users\Provider;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

/**
 * Class UpdatePhotoMail
 * @package App\Mail\Users\Provider
 */
class UpdatePhotoMail extends Mailable implements ShouldQueue
{
    use Queueable;
    use SerializesModels;

    /**
     * @var string
     */
    public $link;

    /**
     * UpdatePhotoMail constructor.
     * @param string $link
     */
    public function __construct(string $link)
    {
        $this->link = $link;
    }

    /**
     * @return UpdatePhotoMail
     */
    public function build()
    {
        return $this->subject('Updated ID Photo Needed')
            ->view('emails.users.provider.update-photo');
    }
}
