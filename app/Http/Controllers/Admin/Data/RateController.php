<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\Data;

use App\Entities\Industry\Rate;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Data\Rate\CreateRequest;
use App\Http\Requests\Admin\Data\Rate\EditRequest;
use App\Repositories\Data\RateRepository;
use App\Repositories\Industry\PositionRepository;
use App\UseCases\Admin\Manage\Data\Positions\RateService;

/**
 * Class RateController
 * @package App\Http\Controllers\Admin\Data
 */
class RateController extends Controller
{
    /**
     * @var RateRepository
     */
    private $rates;
    /**
     * @var RateService
     */
    private $service;
    /**
     * @var PositionRepository
     */
    private $positions;

    public function __construct(
        RateRepository $rates,
        RateService $service,
        PositionRepository $positions
    ) {
        $this->rates = $rates;
        $this->service = $service;
        $this->positions = $positions;
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $rates = $this->rates->findPaginate();
        return view('admin.data.rate.index', compact('rates'));
    }

    /**
     * @param Rate $rate
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show(Rate $rate)
    {
        return view('admin.data.rate.show', compact('rate'));
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        $positions = $this->positions->getAll();
        return view('admin.data.rate.create', compact('positions'));
    }

    /**
     * @param CreateRequest $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function store(CreateRequest $request)
    {
        try {
            $rate = $this->service->create($request);
        } catch (\DomainException $e) {
            return back()->with('error', $e->getMessage());
        }
        return redirect()->route('admin.data.rates.show', $rate);
    }

    /**
     * @param Rate $rate
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit(Rate $rate)
    {
        $positions = $this->positions->getAll();
        $rateArray = $this->rates->rateArray($rate);
        return view('admin.data.rate.edit', compact('rate', 'positions', 'rateArray'));
    }

    /**
     * @param EditRequest $request
     * @param Rate $rate
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function update(EditRequest $request, Rate $rate)
    {
        try {
            $this->service->edit($rate, $request);
        } catch (\DomainException $e) {
            return back()->with('error', $e->getMessage());
        }
        return redirect()->route('admin.data.rates.show', $rate);
    }

    /**
     * @param Rate $rate
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Rate $rate)
    {
        try {
            $this->service->destroy($rate);
        } catch (\DomainException $e) {
            return back()->with('error', $e->getMessage());
        }
        return redirect()->route('admin.data.rates.index');
    }
}
