<?php

namespace App\Http\Controllers\Admin\Analytics;

use App\Http\Controllers\Controller;
use App\Repositories\Admin\Analytics\MapRepository;

/**
 * Class MapController
 * @package App\Http\Controllers\Admin\Analytics
 */
class MapController extends Controller
{
    /**
     * @var MapRepository
     */
    private $mapRepository;

    /**
     * MapController constructor.
     * @param MapRepository $mapRepository
     */
    public function __construct(MapRepository $mapRepository)
    {
        $this->mapRepository = $mapRepository;
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function signupsByAreas()
    {
        $areas = $this->mapRepository->signupsByAreas();
        return view('admin.analytics.signup-areas-map', compact('areas'));
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function signups()
    {
        $signups = $this->mapRepository->signups();
        return view('admin.analytics.signup-map', compact('signups'));
    }

    /**
     * @param string|null $state
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function available(?string $state = null)
    {
        $data = collect($this->mapRepository->findAvailable($state));
        return view('admin.analytics.available', compact('data', 'state'));
    }
}
