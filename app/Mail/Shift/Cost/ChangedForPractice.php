<?php

declare(strict_types=1);

namespace App\Mail\Shift\Cost;

use App\Entities\Shift\Shift;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

/**
 * Class ChangedForPractice
 * @package App\Mail\Shift\Cost
 */
class ChangedForPractice extends Mailable
{
    use SerializesModels;

    /**
     * @var Shift $shift
     */
    public $shift;
    /**
     * @var array
     */
    public $oldCost;

    /**
     * ChangedForPractice constructor.
     * @param Shift $shift
     * @param array $oldCost
     */
    public function __construct(Shift $shift, array $oldCost)
    {
        $this->shift = $shift;
        $this->oldCost = $oldCost;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Update - Shift ' . $this->shift->id)
            ->view('emails.shift.cost.changed-for-practice');
    }
}
