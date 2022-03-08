<?php

declare(strict_types=1);

namespace App\Repositories\Data;

use App\Entities\Industry\Rate;
use Illuminate\Database\Eloquent\Collection;

/**
 * Class RateRepository
 * @package App\Repositories\Data
 */
class RateRepository
{
    /**
     * @param int $id
     * @return Rate
     */
    public function getById(int $id): Rate
    {
        if (!$rate = Rate::where('id', $id)->first()) {
            throw new \DomainException('Rate not found');
        }
        return $rate;
    }

    /**
     * @return Rate[]|Collection
     */
    public function findPaginate()
    {
        return Rate::orderBy('id', 'DESC')->paginate();
    }

    /**
     * @param Rate $rate
     * @param int $position
     * @return bool
     */
    public function isPositionExists(Rate $rate, int $position): bool
    {
        return $rate->positions()
            ->where('id', $position)
            ->exists();
    }

    /**
     * @param Rate $rate
     * @return array
     */
    public function rateArray(Rate $rate): array
    {
        $data['title'] = $rate->title;
        $i = 1;
        foreach ($rate->positions as $position) {
            $data['position'][$i] = $position->id;
            $data['rate'][$i] = $position->pivot->rate;
            $data['minimum_profit'][$i] = $position->pivot->minimum_profit;
            $data['surge_price'][$i] = $position->pivot->surge_price;
            $data['max_day_rate'][$i] = $position->pivot->max_day_rate;
            $i++;
        }
        return $data;
    }
}
