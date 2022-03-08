<?php

declare(strict_types=1);

namespace App\Events\Shift;

use App\Entities\Shift\Shift;
use App\Entities\User\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Class ShiftUpdateEvent
 *
 * Listener {@see \App\Listeners\Shift\ShiftUpdateListener}
 * @package App\Events\Shift
 */
class ShiftUpdateEvent
{
    use Dispatchable;
    use SerializesModels;

    /**
     * @var Shift
     */
    public $shift;

    /**
     * @var string
     */
    public $action;

    /**
     * @var User|null
     */
    public $user;

    /**
     * @var bool|null
     */
    public $send;

    /**
     * ShiftUpdateEvent constructor.
     * @param Shift $shift
     * @param string $action
     * @param User|null $user
     * @param bool|null $send
     */
    public function __construct(Shift $shift, string $action, ?User $user = null, ?bool $send = false)
    {
        $this->shift = $shift;
        $this->action = $action;
        $this->user = $user;
        $this->send = $send;
    }
}
