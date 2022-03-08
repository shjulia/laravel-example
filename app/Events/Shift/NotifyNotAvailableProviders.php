<?php

namespace App\Events\Shift;

use App\Entities\Shift\Shift;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;

/**
 * Class NotifyNotAvailableProviders
 *
 * Listener {@see \App\Listeners\Shift\NotifyNotAvailableProvidersListener}
 * @package App\Events\Shift
 */
class NotifyNotAvailableProviders
{
    use Dispatchable;
    use SerializesModels;

    /**
     * @var Shift
     */
    public $shift;

    /**
     * NotifyNotAvailableProviders constructor.
     * @param Shift $shift
     */
    public function __construct(Shift $shift)
    {
        $this->shift = $shift;
    }
}
