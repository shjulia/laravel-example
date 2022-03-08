<?php

declare(strict_types=1);

namespace App\Mail\Shift;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class YourFeedbackIsImportantMail extends Mailable implements ShouldQueue
{
    use Queueable;
    use SerializesModels;

    /**
     * @var string
     */
    public $name;
    /**
     * @var string
     */
    public $link;
    /**
     * @var string
     */
    public $date;
    /**
     * @var string
     */
    public $photo;

    /**
     * YourFeedbackIsImportantMail constructor.
     * @param string $name
     * @param string $link
     * @param string $date
     * @param string $photo
     */
    public function __construct(string $name, string $link, string $date, string $photo)
    {
        $this->name = $name;
        $this->link = $link;
        $this->date = $date;
        $this->photo = $photo;
    }

    public function build()
    {
        return $this->subject('Your Feedback is Important')
            ->view('emails.shift.feedback');
    }
}
