<?php

declare(strict_types=1);

namespace App\Listeners\Maps\Geocoder;

use App\Entities\User\Practice\PracticeAddress;
use App\Events\Maps\Geocoder\PracticeAddressGeocodeEvent;
use App\Services\Maps\GeocodeService;
use Illuminate\Contracts\Queue\ShouldQueue;

/**
 * Class PracticeAddressGeocodeListener
 *
 * Event {@see \App\Events\Maps\Geocoder\PracticeAddressGeocodeEvent}
 * @package App\Listeners\Maps\Geocoder
 */
class PracticeAddressGeocodeListener implements ShouldQueue
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
     * @param PracticeAddressGeocodeEvent $event
     */
    public function handle(PracticeAddressGeocodeEvent $event): void
    {
        /** @var PracticeAddress $practice */
        $address = $event->address;
        $geocodes = $this->service->getGeocode($address->full_address);
        $address->update([
            'lat' => $geocodes['lat'],
            'lng' => $geocodes['lng']
        ]);
    }
}
