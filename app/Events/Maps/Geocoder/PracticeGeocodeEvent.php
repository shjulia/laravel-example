<?php

declare(strict_types=1);

namespace App\Events\Maps\Geocoder;

use App\Entities\User\Practice\Practice;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;

/**
 * Class PracticeGeocodeEvent
 *
 * Listener {@see \App\Listeners\Maps\Geocoder\PracticeGeocodeListener}
 * @package App\Events\Maps\Geocoder
 */
class PracticeGeocodeEvent
{
    use Dispatchable;
    use SerializesModels;

    /**
     * @var Practice
     */
    public $practice;

    /**
     * PracticeGeocodeEvent constructor.
     * @param Practice $practice
     */
    public function __construct(Practice $practice)
    {
        $this->practice = $practice;
    }
}
