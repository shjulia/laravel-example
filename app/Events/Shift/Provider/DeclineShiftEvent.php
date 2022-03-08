<?php

namespace App\Events\Shift\Provider;

use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

/**
 * Class DeclineShiftEvent
 * Broadcasts Decline shift event to the JavaScript WebSockets application.
 * Channel: job-chanel.provider.decline
 *
 * @package App\Events\Shift\Provider
 */
class DeclineShiftEvent implements ShouldBroadcast
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    /**
     * @var int
     */
    public $practiceId;

    /**
     * @var int
     */
    public $creatorId;

    /**
     * DeclineShiftEvent constructor.
     * @param int $practiceId
     * @param int $creatorId
     */
    public function __construct(int $practiceId, int $creatorId)
    {
        $this->practiceId = $practiceId;
        $this->creatorId = $creatorId;
    }

    /**
     * @return array|\Illuminate\Broadcasting\Channel|\Illuminate\Broadcasting\Channel[]
     */
    public function broadcastOn()
    {
        $private = $this->creatorId;
        return ['job-chanel.provider.decline.' . $private];
    }
}
