<?php

declare(strict_types=1);

namespace App\Listeners\Area;

use App\Entities\Data\Location\Area;
use App\Entities\Data\State;
use App\Entities\User\Practice\Practice;
use App\Entities\User\Provider\Specialist;
use App\Events\Admin\Areas\AreaEvent;
use Illuminate\Contracts\Queue\ShouldQueue;

/**
 * Class AreaListener
 *
 * Event {@see \App\Events\Admin\Areas\AreaEvent}
 * @package App\Listeners\Area
 */
class AreaListener implements ShouldQueue
{
    /**
     * @param AreaEvent $event
     */
    public function handle(AreaEvent $event): void
    {
        /** @var Area $area */
        $area = $event->area;
        /** @var State $state */
        $state = $area->state;
        $zips = $area->zipCodes->pluck('zip')->toArray();
        $cities = $area->cities->pluck('name')->toArray();
        Practice::where('state', $state->short_title)
            ->where(function ($query) use ($zips, $cities) {
                $query->whereIn('city', $cities)
                    ->orWhereIn('zips', $zips);
            })
            ->update([
                'area_id' => $area->id
            ]);

        Specialist::where('driver_state', $state->short_title)
            ->where(function ($query) use ($zips, $cities) {
                $query->whereIn('driver_city', $cities)
                    ->orWhereIn('driver_zip', $zips);
            })
            ->update([
                'area_id' => $area->id
            ]);
    }
}
