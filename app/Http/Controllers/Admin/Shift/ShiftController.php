<?php

namespace App\Http\Controllers\Admin\Shift;

use App\Entities\Shift\Shift;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Shift\EditRequest;
use App\Http\Requests\Admin\Shift\InviteRequest;
use App\Repositories\Shift\ShiftRepository;
use App\Repositories\Statistics\MatchingStepsRepository;
use App\Repositories\User\SpecialistRepository;
use App\UseCases\Admin\Manage\Shift\ShiftService;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

/**
 * Class ShiftController
 * @package App\Http\Controllers\Admin\Statistics
 */
class ShiftController extends Controller
{
    /**
     * @var MatchingStepsRepository
     */
    private $stepsRepository;

    /**
     * @var ShiftRepository
     */
    private $shiftRepository;

    /**
     * @var ShiftService
     */
    private $service;

    /**
     * ShiftController constructor.
     * @param MatchingStepsRepository $stepsRepository
     * @param ShiftRepository $shiftRepository
     * @param ShiftService $service
     */
    public function __construct(
        MatchingStepsRepository $stepsRepository,
        ShiftRepository $shiftRepository,
        ShiftService $service
    ) {
        $this->stepsRepository = $stepsRepository;
        $this->shiftRepository = $shiftRepository;
        $this->service = $service;
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $shifts = $this->shiftRepository->findAll(false);
        $admin = Auth::user();
        return view('admin.shift.index', compact('shifts', 'admin'));
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function archived()
    {
        $shifts = $this->shiftRepository->findAllArchived(false);
        $archived = true;
        return view('admin.shift.index', compact('shifts', 'archived'));
    }

    /**
     * @param Shift $shift
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function shiftLog(Shift $shift)
    {
        $logs = $shift->logs()->with('user')->orderBy('id', 'DESC')->get();
        return view('admin.shift.logs', compact('shift', 'logs'));
    }

    /**
     * @param Shift $shift
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show(Shift $shift)
    {
        $shift = $this->shiftRepository->getById($shift->id);
        $stepsGroups = $shift->steps->groupBy('try')->sortKeysDesc();
        $providerBubbles = [
            '', '', 'What areas could the provider improve on?',
            'What did the provider do well?', 'What were some of the provider\'s strengths?'
        ];
        $practiceBubbles = [
            '', '', 'What areas could the provider improve on?',
            'What did the provider do well?', 'What were some of the provider\'s strengths?'
        ];
        $admin = Auth::user();
        return view('admin.shift.view', compact('shift', 'providerBubbles', 'practiceBubbles', 'stepsGroups', 'admin'));
    }

    /**
     * @param Shift $shift
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function edit(Shift $shift)
    {
        if (!Gate::allows('can-edit-shift-admin', $shift) && !Gate::allows('manage-shifts')) {
            return redirect()->route('admin.shifts.index');
        }
        return view('admin.shift.edit', compact('shift'));
    }

    /**
     * @param EditRequest $request
     * @param Shift $shift
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function update(EditRequest $request, Shift $shift)
    {
        if (!Gate::allows('can-edit-shift-admin', $shift) && !Gate::allows('manage-shifts')) {
            return redirect()->route('admin.shifts.index');
        }
        try {
            $this->service->editProvider($shift, $request->provider_id);
        } catch (\DomainException $e) {
            return back()->with('error', $e->getMessage());
        }
        return redirect()->route('admin.shifts.show', $shift);
    }

    /**
     * @param Shift $shift
     * @return \Illuminate\Http\RedirectResponse
     */
    public function refund(Shift $shift)
    {
        try {
            $this->service->refundCharge($shift);
        } catch (\DomainException $e) {
            return back()->with('error', $e->getMessage());
        }
        return redirect()->route('admin.shifts.show', $shift);
    }

    /**
     * @param Shift $shift
     * @return \Illuminate\Http\RedirectResponse
     */
    public function cancel(Shift $shift)
    {
        try {
            $this->service->cancel($shift);
        } catch (\DomainException $e) {
            return back()->with('error', $e->getMessage());
        }
        return back()->with(['success' => 'Shift have been canceled successfully']);
    }

    /**
     * @param Shift $shift
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function archive(Shift $shift)
    {
        try {
            $this->service->archive($shift);
        } catch (\DomainException $e) {
            return back()->with('error', $e->getMessage());
        }
        return back()->with(['success' => 'Shift have been archived successfully']);
    }

    /**
     * @param Shift $shift
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function inviteProviderForm(Shift $shift)
    {
        return view('admin.shift.invite', compact('shift'));
    }

    /**
     * @param InviteRequest $request
     * @param Shift $shift
     * @param SpecialistRepository $providers
     * @return \Illuminate\Http\JsonResponse
     */
    public function inviteCheck(InviteRequest $request, Shift $shift, SpecialistRepository $providers)
    {
        $provider = $providers->getById($request->provider_id);
        $suite = $provider->isRateSuite($shift->cost / ($shift->shift_time / 60));
        return response()->json($suite, Response::HTTP_OK);
    }

    /**
     * @param InviteRequest $request
     * @param Shift $shift
     * @return \Illuminate\Http\RedirectResponse
     */
    public function inviteProvider(InviteRequest $request, Shift $shift)
    {
        try {
            $this->service->inviteProvider($shift, $request->provider_id);
        } catch (\DomainException $e) {
            return back()->with('error', $e->getMessage());
        }
        return redirect()->route('admin.shifts.show', $shift);
    }

    /**
     * @param Shift $shift
     * @return \Illuminate\Http\RedirectResponse
     */
    public function restartMatching(Shift $shift)
    {
        $shift = $this->shiftRepository->getById($shift->id);
        $this->service->restartMatching($shift);
        return redirect()->route('admin.shifts.show', $shift);
    }
}
