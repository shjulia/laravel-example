<?php

namespace App\Repositories\Data;

use App\Entities\Data\Location\Region;
use App\Entities\Data\State;

/**
 * Class StatesRepository
 * @package App\Repositories\Data
 */
class StatesRepository
{
    /**
     * @return State[]|\Illuminate\Database\Eloquent\Collection
     */
    public function getAll()
    {
        return State::get();
    }

    /**
     * @param string|null $title
     * @return State[]|\Illuminate\Database\Eloquent\Collection
     */
    public function findByTitle(string $title = null)
    {
        if (!empty($title)) {
            return State::where('title', 'like', '%' . $title . '%')->get();
        }
        return $this->getAll();
    }

    /**
     * @param string $name
     * @return State|\Illuminate\Database\Eloquent\Model|object|null
     */
    public function getByShortName(string $name)
    {
        return State::where('short_title', $name)->first();
    }

    /**
     * @param Region $region
     * @return State[]|\Illuminate\Database\Eloquent\Collection
     */
    public function findByRegion(Region $region)
    {
        return State::where('region_id', $region->id)->get();
    }
}
