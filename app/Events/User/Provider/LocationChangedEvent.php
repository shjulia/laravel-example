<?php

declare(strict_types=1);

namespace App\Events\User\Provider;

use App\Entities\User\Provider\Specialist;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Class LocationChangedEvent
 * Sends notification if provider changed location to the JavaScript WebSockets application.
 *
 * @package App\Events\User\Provider
 */
class LocationChangedEvent implements ShouldBroadcast
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    /**
     * @var Specialist
     */
    private $provider;
    /**
     * @var float
     */
    public $lat;
    /**
     * @var float
     */
    public $lng;

    /**
     * LocationChangedEvent constructor.
     * @param Specialist $provider
     */
    public function __construct(Specialist $provider)
    {
        $this->provider = $provider;
        $this->lat = $provider->last_lat;
        $this->lng = $provider->last_lng;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return ['job-chanel.provider-location.' . $this->provider->user_id];
    }
}
