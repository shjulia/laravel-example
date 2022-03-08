<?php

namespace App\Events\Shift;

use App\Entities\Notification\Notification;
use App\Entities\User\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Class NotifyShiftEvent
 * Sends shifts notifications to the JavaScript WebSockets application.
 *
 * @package App\Events\Shift
 */
class NotifyShiftEvent implements ShouldBroadcast
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    /**
     * @var User
     */
    public $user;

    /**
     * @var Notification
     */
    public $notification;

    /**
     * @var string
     */
    public $broadcastChanel;

    /**
     * NotifyShiftEvent constructor.
     * @param User $user
     * @param Notification $notification
     * @param string $broadcastChanel
     */
    public function __construct(User $user, Notification $notification, string $broadcastChanel)
    {
        $this->user = $user;
        $this->notification = $notification;
        $this->broadcastChanel = $broadcastChanel;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        $private = $this->user->id;
        return [$this->broadcastChanel . $private];
    }
}
