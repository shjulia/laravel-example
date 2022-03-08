<?php

namespace App\Http\Controllers\Shift\Practice;

use App\Entities\Data\Score;
use App\Entities\Shift\Shift;
use App\Entities\User\Practice\Practice;
use App\Entities\User\User;
use App\Http\Controllers\Controller;
use App\Http\Requests\Review\ReviewToProviderRequest;
use App\Repositories\Shift\Review\ReviewRepository;
use App\UseCases\Review\Practice\ReviewService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

/**
 * Class ReviewController
 * @package App\Http\Controllers\Shift\Practice
 */
class ReviewController extends Controller
{
    /**
     * @var ReviewService
     */
    private $reviewService;

    /**
     * @var Practice
     */
    private $practice;

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
            $this->practice = $user->practice;
            $shift = $request->shift;
            if (!Gate::allows('can-review-to-provider', $shift)) {
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
    public function review(Shift $shift)
    {
        $scores = $this->reviewRepository->getScoresList(Score::PROVIDER_TYPE);
        return view('shift.review.practice.review', compact('shift', 'scores'));
    }

    /**
     * @param ReviewToProviderRequest $request
     * @param Shift $shift
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function createReviewToProvider(ReviewToProviderRequest $request, Shift $shift)
    {
        try {
            $this->reviewService->createReviewToProvider($this->practice, $shift, $request);
        } catch (\DomainException $e) {
            return redirect()->back()->with(['error' => $e->getMessage()]);
        }
        return redirect()->route('shifts.details', $shift)->with(['success' => 'Review created successfully']);
    }
}
