<?php

declare(strict_types=1);

namespace App\Listeners\Maps;

use App\Entities\User\Practice\Practice;
use App\Events\Maps\PlacePhotoEvent;
use App\Services\Maps\PlaceService;
use Illuminate\Contracts\Queue\ShouldQueue;

/**
 * Class PlacePhotoListener
 * Sets practice photo.
 *
 * Event {@see \App\Events\Maps\PlacePhotoEvent}
 * @package App\Listeners\Maps
 */
class PlacePhotoListener implements ShouldQueue
{
    /**
     * @var PlaceService
     */
    private $placeService;

    /**
     * PlacePhotoListener constructor.
     * @param PlaceService $placeService
     */
    public function __construct(PlaceService $placeService)
    {
        $this->placeService = $placeService;
    }

    /**
     * @param PlacePhotoEvent $event
     */
    public function handle(PlacePhotoEvent $event): void
    {
        /** @var Practice $practice */
        $practice = $event->practice;
        $path = $this->placeService->getPlacePhoto($practice->practice_name, 'USA', $practice->city);
        if (!$path) {
            return;
        }
        $practice->update([
           'practice_photo' => $path
        ]);
    }
}
