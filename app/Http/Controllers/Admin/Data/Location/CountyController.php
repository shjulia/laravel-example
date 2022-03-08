<?php

namespace App\Http\Controllers\Admin\Data\Location;

use App\Entities\Data\Location\City;
use App\Entities\Data\Location\County;
use App\Entities\Data\State;
use App\Http\Requests\Admin\Data\Location\UpdateCountyRequest;
use App\Repositories\Data\Location\CityRepository;
use App\Repositories\Data\Location\CountyRepository;
use Illuminate\Http\Request;

/**
 * Class CountyController
 * @package App\Http\Controllers\Admin\Data\Location
 */
class CountyController
{
    /** @var CountyRepository $countyRepository */
    public $countyRepository;

    /** @var CityRepository $cityRepository */
    public $cityRepository;

    /**
     * CountyController constructor.
     * @param CountyRepository $countyRepository
     * @param CityRepository $cityRepository
     */
    public function __construct(
        CountyRepository $countyRepository,
        CityRepository $cityRepository
    ) {
        $this->countyRepository = $countyRepository;
        $this->cityRepository = $cityRepository;
    }

    /**
     * @param Request $request
     * @param State $state
     * @param County $county
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show(Request $request, State $state, County $county)
    {
        //dd($request->city);
        $cities = $this->cityRepository->getByCounty($county, $request->city ?? null);
        return view('admin.data.location.county.show', compact('county', 'state', 'cities'));
    }

    /**
     * @param State $state
     * @param County $county
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit(State $state, County $county)
    {
        return view('admin.data.location.county.edit', compact('state', 'county'));
    }

    /**
     * @param UpdateCountyRequest $request
     * @param State $state
     * @param County $county
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdateCountyRequest $request, State $state, County $county)
    {
        try {
            $this->countyRepository->edit($county, $request->input());
        } catch (\DomainException $e) {
            return back()->with('error', $e->getMessage());
        }
        return redirect()->route('admin.data.location.county.show', [$state, $county]);
    }
}
