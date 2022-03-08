<?php

namespace App\Http\Controllers\Admin\Data\Location;

use App\Entities\Data\Location\Area;
use App\Entities\Data\State;
use App\Entities\Data\Tier;
use App\Http\Requests\Admin\Data\Location\StoreArea;
use App\Repositories\Data\Location\AreaRepository;
use App\Repositories\Data\Location\CityRepository;
use App\Repositories\Data\Location\ZipCodeRepository;

/**
 * Class AreaController
 * @package App\Http\Controllers\Admin\Data\Location
 */
class AreaController
{
    /** @var AreaRepository $areaRepository */
    private $areaRepository;

    /** @var CityRepository $cityRepository */
    private $cityRepository;

    /** @var ZipCodeRepository $zopCodeRepository */
    private $zopCodeRepository;

    /**
     * AreaController constructor.
     * @param AreaRepository $areaRepository
     * @param CityRepository $cityRepository
     * @param ZipCodeRepository $zipCodeRepository
     */
    public function __construct(
        AreaRepository $areaRepository,
        CityRepository $cityRepository,
        ZipCodeRepository $zipCodeRepository
    ) {
        $this->areaRepository = $areaRepository;
        $this->cityRepository = $cityRepository;
        $this->zopCodeRepository = $zipCodeRepository;
    }

    public function index(State $state)
    {
        $areas = $this->areaRepository->getByState($state);
        return view('admin.data.location.area.index', compact('state', 'areas'));
    }

    /**
     * @param State $state
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create(State $state)
    {
        $cities = $this->cityRepository->getByStateAll($state);
        $zipCodes = $this->zopCodeRepository->getByState($state);
        $tiers = Tier::get();
        return view('admin.data.location.area.create', compact('state', 'cities', 'zipCodes', 'tiers'));
    }

    public function store(StoreArea $request, State $state)
    {
        try {
            $area = $this->areaRepository->create($request->input(), $state);
        } catch (\DomainException $e) {
            return back()->with('error', $e->getMessage());
        }
        return redirect(route('admin.data.location.area.edit', [$state, $area]))->with('success', 'Area was created!');
    }

    public function edit(State $state, Area $area)
    {
        $cities = $this->cityRepository->getByStateAll($state);
        $zipCodes = $this->zopCodeRepository->getByState($state);
        $tiers = Tier::get();
        return view('admin.data.location.area.edit', compact('area', 'state', 'cities', 'zipCodes', 'tiers'));
    }

    public function update(StoreArea $request, State $state, Area $area)
    {
        $area = $this->areaRepository->update($request->input(), $area);
        return redirect(route('admin.data.location.area.edit', [$state, $area]))->with('success', 'Area was updated!');
    }

    public function destroy(State $state, Area $area)
    {
        Area::destroy($area->id);
        return redirect(route('admin.data.location.area.index', [$state]))->with('success', 'Area was deleted!');
    }
}
