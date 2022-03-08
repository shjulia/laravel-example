<?php

namespace App\Repositories\Data\Location;

use App\Entities\Data\Location\Region;
use App\Entities\Data\State;

class RegionRepository
{
    /**
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getAll()
    {
        return Region::paginate(20);
    }

    /**
     * @param array $data
     * @return Region|\Illuminate\Database\Eloquent\Model
     */
    public function create(array $data)
    {
        $region = Region::create([
            'name' => $data['name']
        ]);

        State::whereIn('id', $data['states'])->update([
            'region_id' => $region->id
        ]);

        return $region;
    }

    /**
     * @param Region $region
     * @param array $data
     * @return Region
     */
    public function update(Region $region, array $data)
    {
        //dd($data['states']);
        //dd(State::whereIn('id', $data['states'])->get());
        $region->update([
            'name' => $data['name']
        ]);

        State::where('region_id', $region->id)->update(['region_id' => null]);
        State::whereIn('id', $data['states'])->update([
            'region_id' => $region->id
        ]);

        return $region;
    }

    public function delete(Region $region)
    {
        State::where('region_id', $region->id)->update(['region_id' => null]);
        Region::destroy($region->id);
    }
}
