<?php

namespace App\Events\Shift;

use App\Entities\User\Provider\Specialist;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

/**
 * Class MatchedEvent
 * Broadcasts Matched event to the JavaScript WebSockets application.
 * Channel: job-chanel.provider
 *
 * @package App\Events\Shift
 */
class MatchedEvent implements ShouldBroadcast
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    /**
     * @var int
     */
    public $practiceId;

    /** @var Specialist $provider */
    public $provider;

    /** @var int $shiftId */
    public $shiftId;

    /** @var string  */
    public $provider_photo;

    /** @var int */
    public $creatorId;

    /**
     * MatchedEvent constructor.
     * @param int $practiceId
     * @param Specialist $provider
     * @param int $shiftId
     * @param int $creatorId
     */
    public function __construct(int $practiceId, Specialist $provider, int $shiftId, int $creatorId)
    {
        $this->practiceId = $practiceId;
        $this->provider = $provider;
        $this->provider_photo = $this->provider->photo_url;
        $this->shiftId = $shiftId;
        $this->creatorId = $creatorId;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        $private = $this->creatorId;
        return ['job-chanel.provider.' . $private];
    }
}
