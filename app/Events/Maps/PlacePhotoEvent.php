<?php

namespace App\Events\Maps;

use App\Entities\User\Practice\Practice;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;

/**
 * Class PlacePhotoEvent
 *
 * Listener {@see \App\Listeners\Maps\PlacePhotoListener}
 * @package App\Events\Maps
 */
class PlacePhotoEvent
{
    use Dispatchable;
    use SerializesModels;

    /**
     * @var Practice
     */
    public $practice;

    /**
     * PlacePhotoEvent constructor.
     * @param Practice $practice
     */
    public function __construct(Practice $practice)
    {
        $this->practice = $practice;
    }
}
