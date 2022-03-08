<?php

namespace App\Events\Shift\Provider;

use App\Entities\User\Provider\Specialist;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;

/**
 * Class AcceptShiftEvent
 *
 * Listener {@see \App\Listeners\Shift\Provider\AcceptShiftListener}
 * @package App\Events\Shift\Provider
 */
class AcceptShiftEvent
{
    use Dispatchable;
    use SerializesModels;

    /**
     * @var int
     */
    public $practiceId;

    /** @var Specialist $provider */
    public $provider;

    /** @var int $shiftId */
    public $shiftId;

    /** @var int */
    public $creatorId;

    /**
     * AcceptShiftEvent constructor.
     * @param int $practiceId
     * @param Specialist $provider
     * @param int $shiftId
     * @param int $creatorId
     */
    public function __construct(int $practiceId, Specialist $provider, int $shiftId, int $creatorId)
    {
        $this->practiceId = $practiceId;
        $this->provider = $provider;
        $this->shiftId = $shiftId;
        $this->creatorId = $creatorId;
    }
}
