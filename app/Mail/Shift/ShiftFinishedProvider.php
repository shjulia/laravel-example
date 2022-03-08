<?php

declare(strict_types=1);

namespace App\Mail\Shift;

use App\Entities\Shift\Shift;
use App\Entities\User\Practice\Practice;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ShiftFinishedProvider extends Mailable
{
    use SerializesModels;

    /**
     * @var Practice
     */
    public $practice;
    /**
     * @var Shift
     */
    public $shift;

    /**
     * ShiftFinishedProvider constructor.
     * @param Practice $practice
     * @param Shift $shift
     */
    public function __construct(Practice $practice, Shift $shift)
    {
        $this->practice = $practice;
        $this->shift = $shift;
    }

    public function build()
    {
        return $this->view('emails.shift.shift-finished-provider')
            ->text('emails.shift.text.shift-finished-provider-text');
    }
}
