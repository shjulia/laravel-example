<?php

namespace App\Repositories\Data\Location;

use App\Entities\Data\Location\Area;
use App\Entities\Data\Location\AreaPlaces;
use App\Entities\Data\Location\City;
use App\Entities\Data\Location\ZipCode;
use App\Entities\Data\State;
use App\Entities\User\Practice\Practice;
use App\Entities\User\Provider\Specialist;
use App\Events\Admin\Areas\AreaEvent;
use App\Repositories\User\PracticeRepository;
use App\Repositories\User\SpecialistRepository;

/**
 * Class AreaRepository
 * @package App\Repositories\Data\Location
 */
class AreaRepository
{
    /** @var CityRepository $cityRepository */
    private $cityRepository;

    /** @var ZipCodeRepository $zipRepository */
    private $zipRepository;

    public function __construct(
        CityRepository $cityRepository,
        ZipCodeRepository $zipCodeRepository
    ) {
        $this->cityRepository = $cityRepository;
        $this->zipRepository = $zipCodeRepository;
    }

    /**
     * @param array $data
     * @param State $state
     * @return Area|\Illuminate\Database\Eloquent\Model
     */
    public function create(array $data, State $state)
    {
        $area = Area::create([
            'name' => $data['name'],
            'tier' => $data['tier'],
            'is_open' => $data['is_open'] ?? 0,
            'state_id' => $state->id
        ]);

        if (isset($data['cities'])) {
            $area->cities()->attach($data['cities']);
        }

        if (isset($data['zip_codes'])) {
            $area->zipCodes()->attach($data['zip_codes']);
        }
        event(new AreaEvent($area));
        return $area;
    }

    /**
     * @param array $data
     * @param Area $area
     * @return Area
     */
    public function update(array $data, Area $area)
    {
        $area->update([
            'name' => $data['name'],
            'tier' => $data['tier'],
            'is_open' => $data['is_open'] ?? 0,
        ]);

        if (isset($data['cities'])) {
            $area->cities()->sync($data['cities']);
        } else {
            $area->cities()->sync([]);
        }

        if (isset($data['zip_codes'])) {
            $area->zipCodes()->sync($data['zip_codes']);
        } else {
            $area->zipCodes()->sync([]);
        }
        event(new AreaEvent($area));
        return $area;
    }

    /**
     * @param State $state
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getByState(State $state)
    {
        return Area::where('state_id', $state->id)->paginate(20);
    }

    /**
     * @param null|string $state
     * @param null|string $city
     * @param null|string $zip
     * @return mixed|null
     * @throws \Exception
     */
    public function findByCityOrZip(?string $state = null, ?string $city = null, ?string $zip = null)
    {
        if (!$state || (!$city && !$zip)) {
            return null;
        }

        try {
            if ($zip) {
                /** @var ZipCode|null $zip */
                $zip = $this->zipRepository->getByZip($zip);
            }

            if ($zip) {
                if ($area = AreaPlaces::where('zip_id', $zip->id)->first()) {
                    return $area->area;
                }
            }
            if ($city) {
                /** @var City|null $city */
                $city = $this->cityRepository->getByName($city, $state);
            }
            if ($city) {
                if ($area = AreaPlaces::where('city_id', $city->id)->first()) {
                    return $area->area;
                }
            }
        } catch (\Throwable $e) {
            throw new \Exception('Something went wrong');
        }

        return null;
    }
}
