<?php

namespace App\Http\Controllers\Shift\Practice;

use App\Entities\Shift\Shift;
use App\Entities\User\Practice\Practice;
use App\Entities\User\User;
use App\Exceptions\Shift\NoProvidersAreAvailableException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Shift\LocationRequest;
use App\Http\Requests\Shift\ShiftBaseRequest;
use App\Http\Requests\Shift\TasksRequest;
use App\Http\Requests\Shift\TimeRequest;
use App\Jobs\Shift\FinishShiftJob;
use App\Repositories\Industry\PositionRepository;
use App\Repositories\Industry\TaskRepository;
use App\Repositories\Shift\Coupons\CouponRepository;
use App\Repositories\Shift\ShiftRepository;
use App\UseCases\Shift\FinishService;
use App\UseCases\Shift\ShiftService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

/**
 * Class ShiftController
 * @package App\Http\Controllers\Shift\Practice
 */
class ShiftController extends Controller
{
    /**
     * @var ShiftService
     */
    private $shiftService;

    /**
     * @var ShiftRepository
     */
    private $shiftRepository;

    /**
     * @var PositionRepository
     */
    private $positionRepository;

    /**
     * @var Practice
     */
    private $practice;

    /**
     * @var TaskRepository
     */
    private $taskRepository;

    /**
     * ShiftController constructor.
     * @param ShiftService $shiftService
     * @param ShiftRepository $shiftRepository
     * @param PositionRepository $positionRepository
     * @param TaskRepository $taskRepository
     */
    public function __construct(
        ShiftService $shiftService,
        ShiftRepository $shiftRepository,
        PositionRepository $positionRepository,
        TaskRepository $taskRepository
    ) {
        $this->middleware(function ($request, $next) {
            /** @var User $user */
            $user = Auth::user();
            $this->practice = $user->practice;
            if (
                isset($request->shift)
                && !(isset($request->shift->id) || Gate::allows('can-edit-shift', $request->shift))
            ) {
                abort(403);
            }
            return $next($request);
        });
        $this->middleware(function ($request, $next) {
            /** @var Shift $shift */
            $shift = $request->shift;
            if (!$shift || $shift->isCreatingStatus()) {
                return $next($request);
            }
            if ($shift->isMatchingStatus()) {
                return redirect()->route('shifts.result', $shift);
            }
            if ($shift->isCanceledStatus() || $shift->isNoPrividerFoundStatus() || $shift->isArchived()) {
                return redirect()->route('shifts.index');
            }
            return redirect()->route('shifts.details', $shift);
        })->only(['base', 'createBase', 'time', 'setTime', 'tasks', 'setTasks', 'location', 'setLocation']);
        $this->shiftService = $shiftService;
        $this->shiftRepository = $shiftRepository;
        $this->positionRepository = $positionRepository;
        $this->taskRepository = $taskRepository;
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();
        $practice = $user->practice;
        $shifts = $this->shiftRepository->findFutureShiftsByPractice($practice->id, $user->tz);
        $inProgress = $this->shiftRepository->findProgressShiftsByPractice(
            $practice->id,
            $shifts->pluck('id')->toArray()
        );
        return view('shift.index', compact('practice', 'shifts', 'inProgress'));
    }

    /**
     * @param Request $request
     * @param Shift|null $shift
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function base(Request $request, ?Shift $shift = null)
    {
        $practice = $this->practice;
        $positions = $this->positionRepository->getAllWithChildren();
        $now = (bool)$request->now;
        return view('shift.base', compact('practice', 'shift', 'now', 'positions'));
    }

    /**
     * @param ShiftBaseRequest $request
     * @param Shift|null $shift
     * @return \Illuminate\Http\RedirectResponse
     */
    public function createBase(ShiftBaseRequest $request, ?Shift $shift = null)
    {
        /** @var User $user */
        $user = Auth::user();
        try {
            $shift = $this->shiftService->createBase($this->practice, $user, $request, (bool)$request->now, $shift);
        } catch (\DomainException $e) {
            return redirect()->back()->with(['error' => $e->getMessage()]);
        }
        if ($shift->practice->isAddressesExists()) {
            return redirect()->route('shifts.location', ['shift' => $shift]);
        }
        return redirect()->route('shifts.time', ['shift' => $shift]);
    }

    /**
     * @param Shift $shift
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function location(Shift $shift)
    {
        $practice = $shift->practice;
        return view('shift.location', compact('shift', 'practice'));
    }

    /**
     * @param TimeRequest $request
     * @param Shift $shift
     * @return \Illuminate\Http\RedirectResponse
     */
    public function setLocation(LocationRequest $request, Shift $shift)
    {
        try {
            $this->shiftService->setLocation($shift, $request);
        } catch (\DomainException $e) {
            return redirect()->back()->with(['error' => $e->getMessage()]);
        }
        return redirect()->route('shifts.time', ['shift' => $shift]);
    }

    /**
     * @param Shift $shift
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function time(Shift $shift)
    {
        $previousRoute = $shift->practice->isAddressesExists()
            ? route('shifts.location', ['shift' => $shift])
            : route('shifts.base', $shift);
        $lunchTimes = Shift::getLunchTimes();
        return view('shift.time', compact('shift', 'previousRoute', 'lunchTimes'));
    }

    /**
     * @param TimeRequest $request
     * @param Shift $shift
     * @return \Illuminate\Http\RedirectResponse
     */
    public function setTime(TimeRequest $request, Shift $shift)
    {
        try {
            $this->shiftService->setTime($shift, $request);
        } catch (\DomainException $e) {
            return redirect()->back()->with(['error' => $e->getMessage()]);
        }
        return redirect()->route('shifts.tasks', ['shift' => $shift]);
    }

    /**
     * @param Shift $shift
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function tasks(Shift $shift)
    {
        $tasks = $this->taskRepository->findAllByPosition($shift->position_id);
        $hasSameTime = $this->shiftRepository->findInTheSameTime($shift);
        return view('shift.tasks', compact('shift', 'tasks', 'hasSameTime'));
    }

    /**
     * @param TasksRequest $request
     * @param Shift $shift
     * @return \Illuminate\Http\RedirectResponse
     */
    public function setTasks(TasksRequest $request, Shift $shift)
    {
        try {
            $this->shiftService->setTasks($shift, $request);
        } catch (\DomainException $e) {
            return redirect()->back()->with(['error' => $e->getMessage()]);
        }
        return redirect()->route('shifts.result', ['shift' => $shift]);
    }

    /**
     * @param Shift $shift
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function result(Shift $shift)
    {
        $shift = $this->shiftRepository->getById($shift->id);
        if ($shift->provider_id) {
            return redirect()->route('shifts.details', $shift);
        }
        if ($shift->isNoPrividerFoundStatus()) {
            abort(403, 'No providers found for this shift.');
        }
        if ($shift->isCanceledStatus() || $shift->isArchived()) {
            abort(403, 'Shift was canceled or archived.');
        }
        $this->shiftService->startMatching($shift);
        return view('shift.result', compact('shift'));
    }

    /**
     * @param Shift $shift
     * @return \Illuminate\Http\JsonResponse
     */
    public function cancel(Request $request, Shift $shift)
    {
        try {
            $this->shiftService->cancel($shift, $request->reason, $request->is_rematch ?: false);
        } catch (\DomainException $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_BAD_GATEWAY);
        }
        return response()->json([], Response::HTTP_OK);
    }

    /**
     * @param Request $request
     * @param Shift $shift
     * @return \Illuminate\Http\JsonResponse
     */
    public function coupon(Request $request, CouponRepository $couponRepository, Shift $shift)
    {
        try {
            $coupon = $couponRepository->getByCode($request->coupon);
            $shift = $this->shiftService->applyCoupon($shift, $coupon);
        } catch (\DomainException $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_BAD_GATEWAY);
        }
        return response()->json([
            'cost' => $shift->cost_for_practice,
            'text' => 'Coupon applied successfully.'
        ], Response::HTTP_OK);
    }

    /**
     * @param Shift $shift
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkChanges(Shift $shift)
    {
        try {
            $shift = $this->shiftRepository->getById($shift->id);
        } catch (\DomainException $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_BAD_GATEWAY);
        }
        return response()->json(['potential_provider_id' => $shift->potential_provider_id], Response::HTTP_OK);
    }

    /**
     * @param Shift $shift
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function details(Shift $shift)
    {
        $shift = $this->shiftRepository->getById($shift->id)->setAppends(['completed']);

        if ($shift->isNoPrividerFoundStatus()) {
            abort(403, 'No providers found for this shift.');
        }
        if ($shift->isCanceledStatus() || $shift->isArchived()) {
            abort(403, 'Shift was canceled or archived.');
        }
        if ($shift->multi_days) {
            return redirect()->route('shifts.multipleDetails', $shift);
        }

        return view('shift.details', compact('shift'));
    }

    /**
     * @param Shift $shift
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function multipleDetails(Shift $shift)
    {
        $shift = $this->shiftRepository->getById($shift->id)->setAppends(['completed']);
        if (!$shift->multi_days) {
            abort(403);
        }
        return view('shift.multiple-details', compact('shift'));
    }

    /**
     * @param Shift $shift
     * @return \Illuminate\Http\JsonResponse
     */
    public function findNewProvider(Shift $shift)
    {
        try {
            $this->shiftService->findNewProvider($shift);
        } catch (\DomainException $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_BAD_GATEWAY);
        }
        return response()->json([], Response::HTTP_OK);
    }

    /**
     * @param Shift $shift
     * @return \Illuminate\Http\RedirectResponse
     */
    public function finish(Shift $shift)
    {
        if ($shift->startsInHours() <= 0 && !$shift->isCompleted() && $shift->isAcceptedByProviderStatus()) {
            FinishShiftJob::dispatch($shift);
        } else {
            return redirect()->back()->with(['error' => 'Shift haven\'t started yet.' ]);
        }

        return redirect()->route('shifts.reviews.review', ['shift' => $shift]);
    }
}
