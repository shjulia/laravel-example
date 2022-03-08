<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\Shift;

use App\Entities\Shift\Coupon;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Shift\Coupon\CreateRequest;
use App\Http\Requests\Admin\Shift\Coupon\EditRequest;
use App\Repositories\Industry\PositionRepository;
use App\Repositories\Shift\Coupons\CouponRepository;
use App\UseCases\Admin\Manage\Shift\CouponService;

/**
 * Class CouponController
 * @package App\Http\Controllers\Admin\Shift
 */
class CouponController extends Controller
{
    /**
     * @var CouponService
     */
    private $service;

    /**
     * @var CouponRepository
     */
    private $couponRepository;

    /**
     * CouponController constructor.
     * @param CouponService $service
     * @param CouponRepository $couponRepository
     */
    public function __construct(CouponService $service, CouponRepository $couponRepository)
    {
        $this->service = $service;
        $this->couponRepository = $couponRepository;
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $coupons = $this->couponRepository->findAllCustom();
        $type = "custom";
        return view('admin.shift.coupon.index', compact('coupons', 'type'));
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function indexAuto()
    {
        $coupons = $this->couponRepository->findAllAuto();
        $type = "auto";
        return view('admin.shift.coupon.index', compact('coupons', 'type'));
    }

    /**
     * @param Coupon $coupon
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show(Coupon $coupon)
    {
        return view('admin.shift.coupon.show', compact('coupon'));
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create(PositionRepository $positionRepository)
    {
        $positions = $positionRepository->getAllWithChildren();
        return view('admin.shift.coupon.create', compact('positions'));
    }

    /**
     * @param CreateRequest $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function store(CreateRequest $request)
    {
        try {
            $coupon = $this->service->create($request);
        } catch (\DomainException $e) {
            return back()->with('error', $e->getMessage());
        }
        return redirect()->route('admin.coupons.show', $coupon);
    }

    /**
     * @param PositionRepository $positionRepository
     * @param Coupon $coupon
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit(PositionRepository $positionRepository, Coupon $coupon)
    {
        $positions = $positionRepository->getAllWithChildren();
        return view('admin.shift.coupon.edit', compact('coupon', 'positions'));
    }

    /**
     * @param EditRequest $request
     * @param Coupon $coupon
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function update(EditRequest $request, Coupon $coupon)
    {
        try {
            $this->service->edit($request, $coupon);
        } catch (\DomainException $e) {
            return back()->with('error', $e->getMessage());
        }
        return redirect()->route('admin.coupons.show', $coupon);
    }

    /**
     * @param Coupon $coupon
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Coupon $coupon)
    {
        try {
            $this->service->delete($coupon);
        } catch (\DomainException $e) {
            return back()->with('error', $e->getMessage());
        }
        return redirect()->route('admin.coupons.index');
    }
}
