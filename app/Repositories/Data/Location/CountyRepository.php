<?php

namespace App\Repositories\Data\Location;

use App\Entities\Data\Location\County;
use App\Entities\Data\State;

/**
 * Class CountyRepository
 * @package App\Repositories\Data\Location
 */
class CountyRepository
{
    private $cityRepository;

    public function __construct(CityRepository $cityRepository)
    {
        $this->cityRepository = $cityRepository;
    }

    /**
     * @param State $state
     * @param string|null $name
     * @return County[]|\Illuminate\Database\Eloquent\Collection
     */
    public function getByState(State $state, string $name = null)
    {
        $query = County::where('state', $state->title);
        if (!empty($name)) {
            $query->where('name', 'like', '%' . $name . '%');
        }
        return $query->paginate(20);
    }

    /**
     * @param County $county
     * @param array $data
     * @return bool
     */
    public function edit(County $county, array $data)
    {
        return County::where('id', $county->id)->first()->update([
            'name' => $data['name'],
            'tier' => $data['tier']
        ]);
    }

    public function getCountiesAndCities($state)
    {
        $counties = County::where('state', $state->title)->get()->toArray();
        $cities = $this->cityRepository->getByStateAll($state)->toArray();
        $all = array_merge($cities, $counties);
        return $all;
    }
}
