<?php

namespace App\Http\Controllers\Admin\Analytics;

use App\Entities\Industry\Position;
use App\Entities\User\Practice\Practice;
use App\Entities\User\Provider\Specialist;
use App\Http\Controllers\Controller;
use App\Repositories\Admin\Analytics\TopListRepository;
use App\Repositories\Admin\Analytics\TotalNumberRepository;
use Illuminate\Http\Response;
use App\Http\Requests\Admin\Analytics\DateRequest;
use App\Repositories\Admin\Analytics\PositionRepository;
use App\Repositories\Admin\Analytics\ProfitRepository;
use App\Repositories\Admin\Analytics\WorkedRepository;

class AnalyticsController extends Controller
{
    /**
     * @var ProfitRepository
     */
    private $profitRepository;

    /**
     * @var PositionRepository
     */
    private $positionRepository;

    /**
     * @var WorkedRepository
     */
    private $workedRepository;

    /**
     * @var TotalNumberRepository $totalNumberRepository
     */
    private $totalNumberRepository;

    /** @var TopListRepository $topListRepository */
    private $topListRepository;

    /**
     * AnalyticsController constructor.
     * @param ProfitRepository $profitRepository
     * @param PositionRepository $positionRepository
     * @param TotalNumberRepository $totalNumberRepository
     * @param WorkedRepository $workedRepository
     * @param TopListRepository $topListRepository
     */
    public function __construct(
        ProfitRepository $profitRepository,
        PositionRepository $positionRepository,
        TotalNumberRepository $totalNumberRepository,
        WorkedRepository $workedRepository,
        TopListRepository $topListRepository
    ) {
        $this->profitRepository = $profitRepository;
        $this->positionRepository = $positionRepository;
        $this->workedRepository = $workedRepository;
        $this->totalNumberRepository = $totalNumberRepository;
        $this->topListRepository = $topListRepository;
    }

    public function index()
    {
        $positions = Position::get()->keyBy('id');
        return view('admin.analytics.index', compact('positions'));
    }

    /**
     * @param DateRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getTotalNumber(DateRequest $request)
    {
        $result = $this->totalNumberRepository->getTotalNumber($request->start_date, $request->end_date);

        return response()->json(['result' => $result], Response::HTTP_OK);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function findRejectedToApprovedRatio()
    {
        $result = $this->totalNumberRepository->findRejectedToApprovedRatio();
        return response()->json($result, Response::HTTP_OK);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function approvalTime()
    {
        $result = $this->totalNumberRepository->avgTimeToApproval();
        return response()->json($result, Response::HTTP_OK);
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function approvalTimeDetails()
    {
        $users = $this->totalNumberRepository->findApprovalLogs();
        return view('admin.analytics.details.approval-time', compact('users'));
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function completeTime()
    {
        $result = $this->totalNumberRepository->avgTimeToComplete();
        return response()->json($result, Response::HTTP_OK);
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function completeTimeDetails()
    {
        $users = $this->totalNumberRepository->timesToComplete();
        return view('admin.analytics.details.complete-time', compact('users'));
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function getTopList()
    {
        $providers = $this->topListRepository->getProviders();
        $practices = $this->topListRepository->getPractices();

        return response()->json(['providerData' => $providers, 'practiceData' => $practices], Response::HTTP_OK);
    }

    /**
     * @param bool $top
     * @return \Illuminate\Http\JsonResponse
     */
    public function getRatedTopList(bool $top)
    {
        $providers = $this->topListRepository->ratedProviders($top);
        $practices = $this->topListRepository->ratedPractices($top);

        return response()->json(['providerData' => $providers, 'practiceData' => $practices], Response::HTTP_OK);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function profit(DateRequest $request)
    {
        $res = $this->profitRepository->getEarns($request->start_date, $request->end_date);
        return response()->json($res, Response::HTTP_OK);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function profitByMonth()
    {
        $res = $this->profitRepository->getEarnsByMonths();
        return response()->json($res, Response::HTTP_OK);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function futureByMonth()
    {
        $res = $this->profitRepository->findFutureByMonths();
        return response()->json($res, Response::HTTP_OK);
    }

    /**
     * @param DateRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function providers(DateRequest $request)
    {
        $res = $this->positionRepository->getProvidersWithPosition($request->start_date, $request->end_date);
        return response()->json($res, Response::HTTP_OK);
    }

    /**
     * @param DateRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function revenue(DateRequest $request)
    {
        $res = $this->positionRepository->getRevenueWithPositions($request->start_date, $request->end_date);
        return response()->json($res, Response::HTTP_OK);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function totalWorked()
    {
        $res = $this->workedRepository->getTotalHours();
        return response()->json($res, Response::HTTP_OK);
    }

    /**
     * @param DateRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function totalWorkedPerDay(DateRequest $request)
    {
        $res = $this->workedRepository->getHoursByDays($request->start_date, $request->end_date, $request->position);
        return response()->json($res, Response::HTTP_OK);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function avgMatchingTimeDay()
    {
        $res = $this->workedRepository->matchingTime();
        return response()->json($res, Response::HTTP_OK);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function successPercent()
    {
        $res = $this->workedRepository->successMatchesPercent();
        return response()->json($res, Response::HTTP_OK);
    }

    /**
     * @param DateRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function cancellationReasons(DateRequest $request)
    {
        $res = $this->workedRepository->findCancellationReasons($request->start_date, $request->end_date);
        return response()->json($res, Response::HTTP_OK);
    }
}
