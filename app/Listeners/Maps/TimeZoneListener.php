<?php

declare(strict_types=1);

namespace App\Listeners\Maps;

use App\Entities\User\User;
use App\Events\Maps\TimeZoneEvent;
use App\Services\Maps\TimeZoneService;
use Illuminate\Contracts\Queue\ShouldQueue;

/**
 * Class TimeZoneListener
 * Updates user time zone.
 *
 * Event {@see \App\Events\Maps\TimeZoneEvent}
 * @package App\Listeners\Maps
 */
class TimeZoneListener implements ShouldQueue
{
    /**
     * @var TimeZoneService
     */
    private $service;

    /**
     * TimeZoneListener constructor.
     * @param TimeZoneService $service
     */
    public function __construct(TimeZoneService $service)
    {
        $this->service = $service;
    }

    /**
     * @param TimeZoneEvent $event
     */
    public function handle(TimeZoneEvent $event): void
    {
        /** @var User $user */
        $user = $event->user;
        $tz = null;
        if ($user->isPractice() && $practice = $user->practice) {
            if (!$practice->lat || !$practice->lng) {
                return;
            }
            $tz = $this->service->getTimeZone($practice->lat, $practice->lng);
        } elseif ($user->isProvider() && $provider = $user->specialist) {
            if (!$provider->lat || !$provider->lng) {
                return;
            }
            $tz = $this->service->getTimeZone($provider->lat, $provider->lng);
        }
        if (!$tz) {
            return;
        }
        $user->update([
            'tz' => $tz,
        ]);
    }
}
