<?php

namespace App\Http\Controllers\Admin\Data\Location;

use App\Entities\Data\Location\Region;
use App\Entities\Data\State;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Data\Location\RegionRequest;
use App\Repositories\Data\Location\RegionRepository;
use App\Repositories\Data\StatesRepository;

/**
 * Class RegionController
 * @package App\Http\Controllers\Admin\Data\Location
 */
class RegionController extends Controller
{
    /** @var RegionRepository $regionRepository */
    private $regionRepository;
    /** @var StatesRepository $stateRepository */
    private $stateRepository;

    /**
     * RegionController constructor.
     * @param RegionRepository $regionRepository
     * @param StatesRepository $stateRepository
     */
    public function __construct(RegionRepository $regionRepository, StatesRepository $stateRepository)
    {
        $this->regionRepository = $regionRepository;
        $this->stateRepository = $stateRepository;
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $regions = $this->regionRepository->getAll();
        return view('admin.data.location.region.index', compact('regions'));
    }

    public function show(Region $region)
    {
        $states = $region->states;
        return view('admin.data.location.region.show', compact('region', 'states'));
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        $states = State::get();
        return view('admin.data.location.region.create', compact('states'));
    }

    /**
     * @param RegionRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(RegionRequest $request)
    {
        try {
            $region = $this->regionRepository->create($request->input());
        } catch (\DomainException $e) {
            return back()->with('error', $e->getMessage());
        }
        return redirect(route('admin.data.location.region.show', [$region]))->with('success', 'Region was created!');
    }

    /**
     * @param Region $region
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit(Region $region)
    {
        $states = State::get();
        return view('admin.data.location.region.update', compact('states', 'region'));
    }

    public function update(RegionRequest $request, Region $region)
    {
        try {
            $this->regionRepository->update($region, $request->input());
        } catch (\DomainException $e) {
            return back()->with('error', $e->getMessage());
        }
        return redirect(route('admin.data.location.region.edit', [$region]))->with('success', 'Region was created!');
    }

    public function destroy(Region $region)
    {
        $this->regionRepository->delete($region);
        return redirect(route('admin.data.location.region.index'))->with('success', 'Region was deleted!');
    }
}
