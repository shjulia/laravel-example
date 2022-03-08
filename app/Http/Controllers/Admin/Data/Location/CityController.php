<?php

namespace App\Http\Controllers\Admin\Data\Location;

use App\Entities\Data\Location\City;
use App\Entities\Data\Location\County;
use App\Entities\Data\State;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Data\Location\UpdateCityRequest;
use App\Repositories\Data\Location\CityRepository;

/**
 * Class CityController
 * @package App\Http\Controllers\Admin\Data\Location
 */
class CityController extends Controller
{
    /** @var CityRepository $cityRepository */
    private $cityRepository;

    /**
     * CityController constructor.
     * @param CityRepository $cityRepository
     */
    public function __construct(CityRepository $cityRepository)
    {
        $this->cityRepository = $cityRepository;
    }

    /**
     * @param State $state
     * @param County $county
     * @param City $city
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show(State $state, City $city)
    {
        return view('admin.data.location.city.show', compact('state', 'city'));
    }

    /**
     * @param State $state
     * @param County $county
     * @param City $city
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit(State $state, City $city)
    {
        return view('admin.data.location.city.edit', compact('state', 'city'));
    }

    /**
     * @param UpdateCityRequest $request
     * @param State $state
     * @param County $county
     * @param City $city
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdateCityRequest $request, State $state, City $city)
    {
        try {
            $this->cityRepository->edit($city, $request->input());
        } catch (\DomainException $e) {
            return back()->with('error', $e->getMessage());
        }
        return redirect()->route('admin.data.location.city.show', [$state, $city]);
    }
}
