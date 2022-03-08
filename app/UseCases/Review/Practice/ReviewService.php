<?php

declare(strict_types=1);

namespace App\UseCases\Review\Practice;

use App\Entities\Review\Review;
use App\Entities\Shift\Shift;
use App\Entities\User\Practice\Practice;
use App\Entities\User\Provider\Specialist;
use App\Events\Shift\ShiftUpdateEvent;
use App\Http\Requests\Review\ReviewToProviderRequest;
use App\Jobs\Shift\FiveStarReviewJob;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Class ReviewService
 * Leave review from Practice to Provider after shift is finished.
 *
 * @package App\UseCases\Review\Practice
 */
class ReviewService
{
    /**
     * @param Practice $practice
     * @param Shift $shift
     * @param ReviewToProviderRequest $request
     * @throws \Exception
     */
    public function createReviewToProvider(
        Practice $practice,
        Shift $shift,
        ReviewToProviderRequest $request
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
            $providerReview = $review->providerReview()->create([
                'from_practice_id' => $practice->id,
                'provider_id' => $shift->provider_id
            ]);
            $review->providerReview->scores()->sync($request->score_marks ? explode(',', $request->score_marks) : []);
            /** @var Specialist $provider */
            $provider = $providerReview->provider;
            $provider->update([
                'reviews_total' => $provider->reviews_total + 1,
                'average_stars' => ($provider->average_stars * $provider->reviews_total + $review->score
                    ) / ($provider->reviews_total + 1),

            ]);
            $practice->update([
                'reviews_to_provider_total' => $practice->reviews_to_provider_total + 1,
                'average_stars_to_provider' =>
                    ($practice->average_stars_to_provider * $practice->reviews_to_provider_total + $review->score)
                    / ($practice->reviews_to_provider_total + 1),
            ]);
            if ($shift->isHasReviewFromProvider()) {
                $shift->setFinishedStatus();
                $shift->save();
            }
            DB::commit();
            $user = $provider->user;
            FiveStarReviewJob::dispatch($review, $user, $shift->practice_location->practiceName);
        } catch (\Exception $e) {
            DB::rollBack();
            \LogHelper::error($e);
            throw new \DomainException('Review saving error');
        }

        if ($request->score <= 3) {
            event(new ShiftUpdateEvent(
                $shift,
                'Practice gave rating ' . $request->score . ' to provider.',
                $shift->creator,
                true
            ));
        }
    }
}
