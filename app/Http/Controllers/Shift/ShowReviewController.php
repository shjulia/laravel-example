<?php

namespace App\Http\Controllers\Shift;

use App\Entities\Shift\Shift;
use App\Http\Controllers\Controller;
use App\Repositories\Shift\Review\ReviewRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

/**
 * Class ShowReviewController
 * @package App\Http\Controllers\Shift\Practice
 */
class ShowReviewController extends Controller
{
    /**
     * @var ReviewRepository
     */
    private $reviewRepository;

    /**
     * ShowReviewController constructor.
     * @param ReviewRepository $reviewRepository
     */
    public function __construct(ReviewRepository $reviewRepository)
    {
        $this->reviewRepository = $reviewRepository;
    }

    /**
     * @param Shift $shift
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function watchReviewToProvider(Shift $shift)
    {
        if (!Gate::allows('can-watch-review-to-provider', $shift)) {
            abort(403);
        }
        $review = $this->reviewRepository->getProviderReviewByShift($shift);
        $isPractice = Auth::user()->isPractice();
        return view('shift.review.practice.review-show', compact('shift', 'review', 'isPractice'));
    }

    /**
     * @param Shift $shift
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function watchReviewToPractice(Shift $shift)
    {
        if (!Gate::allows('can-watch-review-to-practice', $shift)) {
            abort(403);
        }
        $review = $this->reviewRepository->getPracticeReviewByShift($shift);
        $isPractice = Auth::user()->isPractice();
        return view('shift.review.provider.review-show', compact('shift', 'review', 'isPractice'));
    }
}
