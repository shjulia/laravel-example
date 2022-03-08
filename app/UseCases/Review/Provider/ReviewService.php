<?php

declare(strict_types=1);

namespace App\UseCases\Review\Provider;

use App\Entities\Review\PracticeReview;
use App\Entities\Review\Review;
use App\Entities\Shift\Shift;
use App\Entities\User\Practice\Practice;
use App\Entities\User\Provider\Specialist;
use App\Events\Shift\ShiftUpdateEvent;
use App\Http\Requests\Review\ReviewToPracticeRequest;
use App\Jobs\Shift\FiveStarReviewJob;
use Illuminate\Support\Facades\DB;

/**
 * Class ReviewService
 * Leave review from Practice to Provider after shift is finished.
 *
 * @package App\UseCases\Review\Provider
 */
class ReviewService
{
    /**
     * @param Specialist $provider
     * @param Shift $shift
     * @param ReviewToPracticeRequest $request
     * @throws \Exception
     */
    public function createReviewToPractice(
        Specialist $provider,
        Shift $shift,
        ReviewToPracticeRequest $request
    ): void {
        DB::beginTransaction();
        try {
            /** @var Review $review */
            $review = Review::create([
                'date' => time(),
                'score' => $request->score,
                'text' => $request->text,
                'shift_id' => $shift->id
            ]);

            $practiceReview = $review->practiceReview()->create([
                'from_provider_id' => $provider->user_id,
                'practice_id' => $shift->practice_id
            ]);
            $review->practiceReview->scores()->sync($request->score_marks ? explode(',', $request->score_marks) : []);
            $provider->update([
                //'hours_total' => $provider->hours_total + round($shift->shift_time / 60),
                //'jobs_total' => $provider->jobs_total + 1,
                'reviews_to_practice_total' => $provider->reviews_to_practice_total + 1,
                'average_stars_to_practice' =>
                    ($provider->average_stars_to_practice * $provider->reviews_to_practice_total + $review->score)
                    / ($provider->reviews_to_practice_total + 1),
            ]);
            /** @var Practice $practice */
            $practice = $shift->practice;
            $practice->update([
                //'hires_total' => $practice->hires_total + 1,
                'reviews_total' => $practice->reviews_total + 1,
                'average_stars' => ($practice->average_stars * $practice->reviews_total + $review->score
                    ) / ($practice->reviews_total + 1),
            ]);
            if ($shift->isHasReviewFromPractice()) {
                $shift->setFinishedStatus();
                $shift->save();
            }
            DB::commit();
            FiveStarReviewJob::dispatch($review, $shift->creator, $provider->user->full_name);
        } catch (\Exception $e) {
            DB::rollBack();
            \LogHelper::error($e);
            throw new \DomainException('Review saving error');
        }

        if ($request->score <= 3) {
            event(new ShiftUpdateEvent(
                $shift,
                'Provider gave rating ' . $request->score . ' to practice.',
                $shift->creator,
                true
            ));
        }
    }
}
