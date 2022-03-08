<?php

namespace App\Repositories\Shift\Review;

use App\Entities\Data\Score;
use App\Entities\Review\Review;
use App\Entities\Shift\Shift;

/**
 * Class ReviewRepository
 * @package App\Repositories\Shift\Review
 */
class ReviewRepository
{
    /**
     * @param Shift $shift
     * @return Review
     */
    public function getProviderReviewByShift(Shift $shift): Review
    {
        $review = Review::where('shift_id', $shift->id)
            ->whereHas('providerReview')
            ->with('providerReview')
            ->first();
        if (!$review) {
            throw new \DomainException('Review not found');
        }
        return $review;
    }

    /**
     * @param Shift $shift
     * @return Review
     */
    public function getPracticeReviewByShift(Shift $shift): Review
    {
        $review = Review::where('shift_id', $shift->id)
            ->whereHas('practiceReview')
            ->with('practiceReview')
            ->first();
        if (!$review) {
            throw new \DomainException('Review not found');
        }
        return $review;
    }

    /**
     * @param string $type
     * @return Score[]
     */
    public function getScoresList(string $type)
    {
        return Score::where(['for_type' => $type, 'active' => 1])->get();
    }
}
