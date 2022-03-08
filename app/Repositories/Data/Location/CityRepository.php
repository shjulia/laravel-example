<?php

namespace App\Repositories\Data\Location;

use App\Entities\Data\Location\City;
use App\Entities\Data\Location\County;
use App\Entities\Data\State;
use App\Repositories\Data\StatesRepository;

/**
 * Class CityRepository
 * @package App\Repositories\Data\Location
 */
class CityRepository
{
    private $stateRepository;

    public function __construct(StatesRepository $stateRepository)
    {
        $this->stateRepository = $stateRepository;
    }

    /**
     * @param County $county
     * @param string|null $name
     * @return City[]|\Illuminate\Database\Eloquent\Collection
     */
    public function getByCounty(County $county, string $name = null)
    {
        $query = City::where('county', $county->id);
        if (!empty($name)) {
            $query->where('name', 'like', '%' . $name . '%');
        }
        return $query->paginate(20);
    }

    /**
     * @param State $state
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getByState(State $state)
    {
        return City::where('state', $state->id)->paginate(20);
    }

    /**
     * @param State $state
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getByStateAll(State $state)
    {
        return City::where('state', $state->id)->get();
    }

    /**
     * @param string $name
     * @param string $state
     * @return City|\Illuminate\Database\Eloquent\Model|object|null
     */
    public function getByName(string $name, string $state)
    {
        $state = $this->stateRepository->getByShortName($state);
        if (!$state) {
            return null;
        }
        return City::where('name', 'like', '%' . $name . '%')->where('state', $state->id)->first();
    }

    /**
     * @param $name
     * @param State $state
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function searchByName($name, State $state)
    {
        return City::where('name', 'like', '%' . $name . '%')->where('state', $state->id)->paginate(20);
    }

    /**
     * @param City $city
     * @param array $data
     * @return bool
     */
    public function edit(City $city, array $data)
    {
        return City::where('id', $city->id)->first()->update([
            'name' => $data['name'],
            'tier' => $data['tier']
        ]);
    }
}
