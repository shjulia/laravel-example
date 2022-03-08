<?php

declare(strict_types=1);

namespace App\Events\Maps;

use App\Entities\User\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Class TimeZoneEvent
 *
 * Listener {@see \App\Listeners\Maps\TimeZoneListener}
 * @package App\Events\Maps
 */
class TimeZoneEvent
{
    use Dispatchable;
    use SerializesModels;

    /**
     * @var User
     */
    public $user;

    /**
     * TimeZoneEvent constructor.
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }
}
