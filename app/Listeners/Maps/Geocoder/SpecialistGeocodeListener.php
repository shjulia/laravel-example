<?php

declare(strict_types=1);

namespace App\Listeners\Maps\Geocoder;

use App\Entities\User\Provider\Specialist;
use App\Events\Maps\Geocoder\SpecialistGeocodeEvent;
use App\Services\Maps\GeocodeService;
use Illuminate\Contracts\Queue\ShouldQueue;

/**
 * Class SpecialistGeocodeListener
 *
 * Event {@see \App\Events\Maps\Geocoder\SpecialistGeocodeEvent}
 * @package App\Listeners\Maps\Geocoder
 */
class SpecialistGeocodeListener implements ShouldQueue
{
    /**
     * @var GeocodeService
     */
    private $service;

    /**
     * PracticeGeocodeListener constructor.
     * @param GeocodeService $service
     */
    public function __construct(GeocodeService $service)
    {
        $this->service = $service;
    }

    /**
     * @param SpecialistGeocodeEvent $event
     */
    public function handle(SpecialistGeocodeEvent $event): void
    {
        /** @var Specialist $specialist */
        $specialist = $event->specialist;
        $geocodes = $this->service->getGeocode($specialist->full_address);
        $specialist->update([
            'lat' => $geocodes['lat'],
            'lng' => $geocodes['lng']
        ]);
    }
}
