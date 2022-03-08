<?php

declare(strict_types=1);

namespace App\Mail\Shift;

use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class FiveStarReviewMail extends Mailable
{
    use SerializesModels;

    /**
     * @var string
     */
    private $name;
    /**
     * @var int
     */
    private $date;

    public function __construct(string $name, string $date)
    {
        $this->name = $name;
        $this->date = $date;
    }

    public function build()
    {
        return $this->subject('You Are Awesome')
            ->view('emails.shift.five-star-review')
            ->with(['name' => $this->name, 'date' => $this->date]);
    }
}
