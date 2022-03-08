<?php

declare(strict_types=1);

namespace App\Entities\Review;

use App\Entities\Data\Score;
use App\Entities\User\Practice\Practice;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class PracticeReview - Review from provider to practice
 *
 * @package App\Entities\Review
 * @property int $review_id
 * @property int $from_provider_id
 * @property int $practice_id
 * @property-read \App\Entities\User\Practice\Practice $practice
 * @property-read \App\Entities\Review\Review $review
 * @property-read Collection|\App\Entities\Data\Score[] $scores what practice did well
 * @mixin \Eloquent
 */
class PracticeReview extends Model
{
    /**
     * @var string
     */
    protected $primaryKey = "review_id";

    /**
     * @var array
     */
    protected $guarded = [];

    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * 1 to 1 to review
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function review()
    {
        return $this->belongsTo(Review::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function practice()
    {
        return $this->belongsTo(Practice::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function scores()
    {
        return $this->belongsToMany(
            Score::class,
            'practice_reviews_scores',
            'practice_review_id',
            'score_id'
        );
    }
}
