<?php

namespace App\Http\Controllers\Shift\Provider;

use App\Entities\Data\Score;
use App\Entities\Shift\Shift;
use App\Entities\User\Provider\Specialist;
use App\Entities\User\User;
use App\Http\Controllers\Controller;
use App\Http\Requests\Shift\Provider\MultiDayRequest;
use App\Jobs\Shift\FinishShiftJob;
use App\Repositories\Payment\ProviderBonusesRepository;
use App\Repositories\Payment\ProviderChargeRepository;
use App\Repositories\Shift\ProviderMoneyRepository;
use App\Repositories\Shift\Review\ReviewRepository;
use App\Repositories\Shift\ShiftRepository;
use App\Repositories\User\SpecialistRepository;
use App\UseCases\Shift\Provider\ShiftService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

/**
 * Class ShiftController
 * @package App\Http\Controllers\Shift\Provider
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
     * @var Specialist
     */
    private $provider;

    /**
     * @var ReviewRepository
     */
    private $reviewRepository;
    /**
     * @var ProviderChargeRepository
     */
    private $providerChargeRepository;
    /**
     * @var ProviderBonusesRepository
     */
    private $providerBonusesRepository;

    /**
     * ShiftController constructor.
     * @param ShiftService $shiftService
     * @param ShiftRepository $shiftRepository
     * @param ReviewRepository $reviewRepository
     * @param ProviderChargeRepository $providerChargeRepository
     * @param ProviderBonusesRepository $providerBonusesRepository
     */
    public function __construct(
        ShiftService $shiftService,
        ShiftRepository $shiftRepository,
        ReviewRepository $reviewRepository,
        ProviderChargeRepository $providerChargeRepository,
        ProviderBonusesRepository $providerBonusesRepository
    ) {
        $this->middleware(function ($request, $next) {
            /** @var User $user */
            $user = Auth::user();
            $this->provider = $user->specialist;
            try {
                $this->shiftService->check($request->shift, $this->provider);
            } catch (\DomainException $e) {
                abort(403, $e->getMessage());
            }
            return $next($request);
        })->only(['acceptPage', 'accept', 'decline', 'multipleAccept', 'viewInvite']);
        $this->shiftService = $shiftService;
        $this->shiftRepository = $shiftRepository;
        $this->reviewRepository = $reviewRepository;
        $this->providerChargeRepository = $providerChargeRepository;
        $this->providerBonusesRepository = $providerBonusesRepository;
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(?Shift $shift = null)
    {
        /** @var User $user */
        $user = Auth::user();
        $provider = $user->specialist;
        $shifts = $this->shiftRepository->findShiftsByProvider($provider);
        $shifts_dates = $this->shiftService->getShiftsDates($shifts);
        $bonusMoney = $this->providerBonusesRepository->sumNotSentToPaid($provider);
        $providerMoney = $this->providerChargeRepository->sumNotSentToPaid($provider) + $bonusMoney;
        $scores = $this->reviewRepository->getScoresList(Score::PROVIDER_TYPE);
        $item = !$shift ? null : $this->shiftRepository->findShiftByProviderAndId($shift->id, $user->id);

        return view('shift.provider.index', compact(
            'shifts',
            'provider',
            'user',
            'shifts_dates',
            'providerMoney',
            'scores',
            'item'
        ));
    }

    /**
     * @param Shift $shift
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function acceptPage(Shift $shift)
    {
        $provider = $this->provider;
        $practice = $shift->practice;
        return view('shift.provider.accept', compact('shift', 'provider', 'practice'));
    }

    /**
     * @param Shift $shift
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function accept(Shift $shift, Request $request)
    {
        try {
            $this->shiftService->accept($shift, $request, $this->provider);
        } catch (\DomainException $e) {
            return response()->json($e->getMessage(), Response::HTTP_BAD_GATEWAY);
        }
        return response()->json('Success', Response::HTTP_OK);
    }

    /**
     * @param Shift $shift
     * @param MultiDayRequest $request
     * @return \Illuminate\Http\RedirectResponse|string
     * @throws \Exception
     */
    public function multipleAccept(Shift $shift, MultiDayRequest $request)
    {
        try {
            $this->shiftService->multipleAccept($shift, $request, $this->provider);
        } catch (\DomainException $e) {
            return back()->with(['error' => $e->getMessage()]);
        }
        return redirect()->route('shifts.provider.index');
    }

    /**
     * @param Shift $shift
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function decline(Shift $shift)
    {
        try {
            $this->shiftService->decline($shift, $this->provider);
        } catch (\DomainException $e) {
            return response()->json($e->getMessage(), Response::HTTP_BAD_GATEWAY);
        }
        return redirect()->route('shifts.provider.index');
    }

    /**
     * @param Shift $shift
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function viewInvite(Shift $shift)
    {
        try {
            $this->shiftService->view($shift, $this->provider);
        } catch (\DomainException $e) {
            return response()->json($e->getMessage(), Response::HTTP_BAD_GATEWAY);
        }
        return response()->json([], Response::HTTP_OK);
    }

    /**
     * @param Shift $shift
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showResult(Shift $shift)
    {
        $practice = $shift->practice;
        return view('shift.provider.result-show', compact('shift', 'practice'));
    }

    /**
     * @param Shift $shift
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function finish(Shift $shift, Request $request)
    {
        if (!Gate::allows('provider-view-shift', $shift)) {
            return response()->json(['error' => "You can't allow this page"], Response::HTTP_FORBIDDEN);
        }
        if (!Gate::allows('finish-shift', $shift)) {
            return response()->json(['error' => "You can't finish shift."], Response::HTTP_FORBIDDEN);
        }
        try {
            $this->shiftService->finish($shift, $request->lat, $request->lng);
        } catch (\DomainException $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_BAD_GATEWAY);
        }
        return response()->json(
            ['route' => route('shifts.provider.reviews.review', ['shift' => $shift])],
            Response::HTTP_OK
        );
    }

    /**
     * @param Shift $shift
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function start(Shift $shift, Request $request)
    {
        if (!Gate::allows('start-shift', $shift)) {
            return response()->json(['error' => 'Shift already started'], Response::HTTP_FORBIDDEN);
        }
        try {
            $this->shiftService->start($shift, $request->lat, $request->lng);
        } catch (\DomainException $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_BAD_GATEWAY);
        }
        return response()->json([], Response::HTTP_OK);
    }

    /**
     * Change user availability
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|Response
     */
    public function available(Request $request)
    {
        $user = auth()->user();

        try {
            Specialist::where('user_id', $user->id)->update([
                'available' => (int) $request->get('available')
            ]);
        } catch (\DomainException $e) {
            return response($e->getMessage(), Response::HTTP_BAD_GATEWAY);
        }

        return response('Success', Response::HTTP_OK);
    }
}
