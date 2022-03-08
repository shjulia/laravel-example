<?php

declare(strict_types=1);

namespace App\Listeners\Maps\Geocoder;

use App\Entities\User\Practice\Practice;
use App\Events\Maps\Geocoder\PracticeGeocodeEvent;
use App\Services\Maps\GeocodeService;
use Illuminate\Contracts\Queue\ShouldQueue;

/**
 * Class PracticeGeocodeListener
 *
 * Event {@see \App\Events\Maps\Geocoder\PracticeGeocodeEvent}
 * @package App\Listeners\Maps\Geocoder
 */
class PracticeGeocodeListener implements ShouldQueue
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
     * @param PracticeGeocodeEvent $event
     */
    public function handle(PracticeGeocodeEvent $event): void
    {
        /** @var Practice $practice */
        $practice = $event->practice;
        $geocodes = $this->service->getGeocode($practice->full_address);
        $practice->update([
            'lat' => $geocodes['lat'],
            'lng' => $geocodes['lng']
        ]);
    }
}
