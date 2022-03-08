<?php

namespace App\Http\Controllers\Shift\Provider;

use App\Entities\Data\Score;
use App\Entities\Shift\Shift;
use App\Entities\User\Provider\Specialist;
use App\Entities\User\User;
use App\Http\Controllers\Controller;
use App\Http\Requests\Review\ReviewToPracticeRequest;
use App\Repositories\Shift\Review\ReviewRepository;
use App\Repositories\Shift\ShiftRepository;
use App\UseCases\Review\Provider\ReviewService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

/**
 * Class ReviewController
 * @package App\Http\Controllers\Shift\Provider
 */
class ReviewController extends Controller
{
    /**
     * @var ReviewService
     */
    private $reviewService;

    /**
     * @var Specialist
     */
    private $provider;

    /**
     * @var ReviewRepository
     */
    private $reviewRepository;

    /**
     * ReviewController constructor.
     * @param ReviewService $reviewService
     * @param ReviewRepository $reviewRepository
     */
    public function __construct(ReviewService $reviewService, ReviewRepository $reviewRepository)
    {
        $this->middleware(function ($request, $next) {
            /** @var User $user */
            $user = Auth::user();
            $this->provider = $user->specialist;
            $shift = $request->shift;
            if (!Gate::allows('can-review-to-practice', $shift)) {
                abort(403);
            }
            return $next($request);
        });
        $this->reviewService = $reviewService;
        $this->reviewRepository = $reviewRepository;
    }

    /**
     * @param Shift $shift
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function review(Shift $shift, ShiftRepository $shiftRepository)
    {
        $shifts = [];
        $provider = auth()->user()->specialist;
        $review = 1;
        $scores = $this->reviewRepository->getScoresList(Score::PRACTICE_TYPE);
        $shift = $shiftRepository->findShiftByProviderAndId($shift->id, $provider->user_id);
        return view(
            'shift.provider.index',
            compact(
                'shift',
                'shifts',
                'provider',
                'review',
                'scores'
            )
        );
    }

    /**
     * @param ReviewToPracticeRequest $request
     * @param Shift $shift
     * @return \Illuminate\Http\RedirectResponse
     */
    public function createReviewToProvider(ReviewToPracticeRequest $request, Shift $shift)
    {
        try {
            $this->reviewService->createReviewToPractice($this->provider, $shift, $request);
        } catch (\DomainException $e) {
            return redirect()->back()->with(['error' => $e->getMessage()]);
        }
        return redirect()->route('shifts.provider.index')->with(['success' => 'Review created successfully']);
    }
}
