<?php

declare(strict_types=1);

namespace App\Events\Shift;

use App\Entities\Shift\Shift;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Class AlmostFinishedShiftEvent
 *
 * Listener {@see \App\Listeners\Shift\AlmostFinishedShiftListener}
 * @package App\Events\Shift
 */
class AlmostFinishedShiftEvent
{
    use Dispatchable;
    use SerializesModels;

    /**
     * @var Shift
     */
    public $shift;

    /**
     * AlmostFinishedShiftEvent constructor.
     * @param Shift $shift
     */
    public function __construct(Shift $shift)
    {
        $this->shift = $shift;
    }
}
