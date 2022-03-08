<?php

declare(strict_types=1);

namespace App\Mail\Shift\Finish;

use App\Entities\Shift\Shift;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

/**
 * Class FinishShiftPractice
 * @package App\Mail\Shift\Finish
 */
class FinishShiftPractice extends Mailable
{
    use SerializesModels;

    /**
     * @var Shift $shift
     */
    public $shift;

    /**
     * FinishShiftPractice constructor.
     * @param Shift $shift
     */
    public function __construct(Shift $shift)
    {
        $this->shift = $shift;
    }

    /**
     * @return FinishShiftPractice
     */
    public function build()
    {
        return $this->view('emails.shift.finish.practice');
    }
}
