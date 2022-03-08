<?php

namespace App\Events\Shift;

use App\Entities\Shift\Shift;
use App\Entities\User\User;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;

/**
 * Class PaymentEvent
 *
 * Listener {@see \App\Listeners\Shift\PaymentListener}
 * @package App\Events\Shift
 */
class PaymentEvent
{
    use Dispatchable;
    use SerializesModels;

    /**
     * @var Shift
     */
    public $shift;

    /**
     * PaymentEvent constructor.
     *
     * @param $provider
     * @param $shift
     */
    public function __construct(Shift $shift)
    {
        $this->shift = $shift;
    }
}
