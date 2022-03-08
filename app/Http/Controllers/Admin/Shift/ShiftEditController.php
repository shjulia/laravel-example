<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\Shift;

use App\Entities\Payment\ProviderCharge;
use App\Entities\Shift\Shift;
use App\Entities\User\User;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Shift\ProviderChargeEditRequest;
use App\Http\Requests\Shift\TimeRequest;
use App\Jobs\Shift\Provider\PaymentJob;
use App\UseCases\Admin\Manage\Shift\ShiftEditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Class ShiftEditController
 * @package App\Http\Controllers\Admin\Shift
 */
class ShiftEditController extends Controller
{
    /**
     * @var ShiftEditService
     */
    private $shiftEditService;

    /**
     * ShiftEditController constructor.
     * @param ShiftEditService $shiftEditService
     */
    public function __construct(ShiftEditService $shiftEditService)
    {
        $this->shiftEditService = $shiftEditService;
    }

    /**
     * @param Shift $shift
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function time(Shift $shift)
    {
        $previousRoute = route('admin.shifts.show', $shift);
        $lunchTimes = Shift::getLunchTimes();
        return view('admin.shift.edit.time', compact('shift', 'lunchTimes', 'previousRoute'));
    }

    /**
     * @param TimeRequest $request
     * @param Shift $shift
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function setTime(TimeRequest $request, Shift $shift)
    {
        $admin = Auth::user();
        try {
            $this->shiftEditService->changeTime($shift, $request, $admin);
        } catch (\DomainException $e) {
            return redirect()->back()->with(['error' => $e->getMessage()]);
        }
        return redirect()->route('admin.shifts.show', ['shift' => $shift])
            ->with(['success' => 'Successfully changed']);
    }

    /**
     * @param ProviderCharge $charge
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function editProviderCharge(ProviderCharge $charge)
    {
        $shift = $charge->shift;
        $systems = ProviderCharge::paymentSystemLists();
        $statuses = ProviderCharge::statusesLists();
        return view('admin.shift.edit.provider-charge', compact('shift', 'charge', 'systems', 'statuses'));
    }

    /**
     * @param Shift $shift
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function changeBonus(Shift $shift, Request $request)
    {
        /** @var User $admin */
        $admin = Auth::user();
        try {
            if (!is_numeric($request->bonus) || $request->bonus < 0) {
                throw new \DomainException('Bonus should be > 0');
            }
            $this->shiftEditService->changeBonus($shift, (float)$request->bonus, $admin);
        } catch (\DomainException $e) {
            return redirect()->back()->with(['error' => $e->getMessage()]);
        }
        return redirect()->route('admin.shifts.show', ['shift' => $shift])
            ->with(['success' => 'Successfully changed']);
    }
}
