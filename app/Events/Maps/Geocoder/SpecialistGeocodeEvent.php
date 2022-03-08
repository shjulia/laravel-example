<?php

declare(strict_types=1);

namespace App\Events\Maps\Geocoder;

use App\Entities\User\Provider\Specialist;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;

/**
 * Class SpecialistGeocodeEvent
 *
 * Listener {@see \App\Listeners\Maps\Geocoder\SpecialistGeocodeListener}
 * @package App\Events\Maps\Geocoder
 */
class SpecialistGeocodeEvent
{
    use Dispatchable;
    use SerializesModels;

    /**
     * @var Specialist
     */
    public $specialist;

    /**
     * SpecialistGeocodeEvent constructor.
     * @param Specialist $specialist
     */
    public function __construct(Specialist $specialist)
    {
        $this->specialist = $specialist;
    }
}
