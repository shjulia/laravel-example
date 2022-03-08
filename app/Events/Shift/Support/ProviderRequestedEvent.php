<?php

declare(strict_types=1);

namespace App\Events\Shift\Support;

use App\Entities\Shift\Shift;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Class ProviderRequestedEvent
 *
 * Listener {@see \App\Listeners\Shift\Support\ProviderRequestedListener}
 * @package App\Events\Shift\Support
 */
class ProviderRequestedEvent
{
    use Dispatchable;
    use SerializesModels;

    /**
     * @var Shift
     */
    public $shift;

    /**
     * ProviderRequestedEvent constructor.
     * @param Shift $shift
     */
    public function __construct(Shift $shift)
    {
        $this->shift = $shift;
    }
}
