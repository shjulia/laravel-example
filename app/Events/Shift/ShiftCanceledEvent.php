<?php

declare(strict_types=1);

namespace App\Events\Shift;

use App\Entities\Shift\Shift;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Class ShiftCanceledEvent
 *
 * Listener {@see \App\Listeners\Shift\ShiftCanceledListener}
 * @package App\Events\Shift
 */
class ShiftCanceledEvent
{
    use Dispatchable;
    use SerializesModels;

    /**
     * @var Shift
     */
    public $shift;

    /**
     * ShiftCanceledEvent constructor.
     * @param Shift $shift
     */
    public function __construct(Shift $shift)
    {
        $this->shift = $shift;
    }
}
