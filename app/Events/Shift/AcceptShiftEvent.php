<?php

namespace App\Events\Shift;

use App\Entities\Shift\Shift;
use App\Entities\User\User;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

/**
 * Class AcceptShiftEvent
 *
 * Listener {@see \App\Listeners\Shift\AcceptShiftListener}
 * @package App\Events\Shift
 */
class AcceptShiftEvent
{
    use Dispatchable;
    use SerializesModels;

    /**
     * @var User
     */
    public $user;

    /**
     * @var Shift
     */
    public $shift;

    /**
     * AcceptShiftEvent constructor.
     * @param User $user
     * @param Shift $shift
     */
    public function __construct(User $user, Shift $shift)
    {
        $this->user = $user;
        $this->shift = $shift;
    }
}
