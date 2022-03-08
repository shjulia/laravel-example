<?php

namespace App\Repositories\Data;

use App\Entities\Data\Distance;
use App\Entities\DTO\Distance\Distance as DistanceDTO;
use App\Entities\Shift\Shift;

/**
 * Class DistanceRepository
 * @package App\Repositories\Data
 */
class DistanceRepository
{
    /** @var int  */
    public const MAX_DISTANCE = 250;

    /** @var int  */
    public const OTHER_AREA_LIMIT = 3;

    /**
     * @param int $providerId
     * @param int $practiceId
     * @param int|null $addressId
     * @param bool|null $isText
     * @return float|mixed|string|null
     */
    public function findDistance(int $providerId, int $practiceId, ?int $addressId = null, ?bool $isText = false)
    {
        $distance = Distance::where([
            'provider_id' => $providerId,
            'practice_id' => $practiceId,
            'address_id' => $addressId
        ])->first();
        if ($distance) {
            return !$isText ? $distance->duration : $distance->duration_text;
        }
        return null;
    }

    /**
     * @param int $providerId
     * @param int $practiceId
     * @param int|null $addressId
     * @param bool|null $isText
     * @return Distance|\Illuminate\Database\Eloquent\Model|object|null
     */
    public function findFullDistance(int $providerId, int $practiceId, ?int $addressId = null, ?bool $isText = false)
    {
        return Distance::where([
            'provider_id' => $providerId,
            'practice_id' => $practiceId,
            'address_id' => $addressId
        ])->first();
    }

    /**
     * @param int $providerId
     * @param int $practiceId
     * @param int|null $addressId
     * @param DistanceDTO|null $distanceDTO
     * @return Distance|null
     */
    public function createDistance(
        int $providerId,
        int $practiceId,
        ?int $addressId = null,
        ?DistanceDTO $distanceDTO = null
    ): ?Distance {
        if (!$distanceDTO) {
            return null;
        }
        return Distance::create([
            'provider_id' => $providerId,
            'practice_id' => $practiceId,
            'address_id' => $addressId,
            'duration' => $distanceDTO->durationVal,
            'duration_text' => $distanceDTO->durationText,
            'distance' => $distanceDTO->distanceVal,
            'distance_text' => $distanceDTO->distanceText
        ]);
    }

    /**
     * @param Shift $shift
     * @param array $providers
     * @return Distance[]|\Illuminate\Database\Eloquent\Collection
     */
    public function findForShift(Shift $shift, array $providers)
    {
        return Distance::where('practice_id', $shift->practice->id)
            ->where('address_id', $shift->practice_location->addressId)
            ->where('distance', '<=', self::MAX_DISTANCE * 0.621371 * 1000)
            ->whereIn('provider_id', $providers)
            ->orderBy('distance')
            ->limit(self::OTHER_AREA_LIMIT)
            ->get();
    }
}
