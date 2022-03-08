<?php

namespace App\Http\Controllers\Admin\Data\Location;

use App\Entities\Data\Location\City;
use App\Entities\Data\State;
use App\Http\Controllers\Controller;
use App\Repositories\Data\Location\CityRepository;
use App\Repositories\Data\Location\CountyRepository;
use App\Repositories\Data\StatesRepository;
use Illuminate\Http\Request;

/**
 * Class StateController
 * @package App\Http\Controllers\Admin\Data\Location
 */
class StateController extends Controller
{
    /** @var StatesRepository $statesRepository */
    private $statesRepository;

    /** @var CityRepository $cityRepository */
    private $cityRepository;

    /**
     * StateController constructor.
     * @param StatesRepository $statesRepository
     * @param CityRepository $cityRepository
     */
    public function __construct(
        StatesRepository $statesRepository,
        CityRepository $cityRepository
    ) {
        $this->statesRepository = $statesRepository;
        $this->cityRepository = $cityRepository;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $states = $this->statesRepository->findByTitle($request->state ?? null);
        return view('admin.data.location.state.index', compact('states'));
    }

    /**
     * @param State $state
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show(Request $request, State $state)
    {
        if ($request->city) {
            $cities = $this->cityRepository->searchByName($request->city, $state);
        } else {
            $cities = $this->cityRepository->getByState($state);
        }
        return view('admin.data.location.state.show', compact('state', 'cities'));
    }
}
