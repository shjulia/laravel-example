<?php

declare(strict_types=1);

namespace App\Entities\Review;

use App\Entities\Data\Score;
use App\Entities\User\Provider\Specialist;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ProviderReview - review from practice to provider
 *
 * @package App\Entities\Review
 * @property int $review_id
 * @property int $from_practice_id
 * @property int $provider_id
 * @property-read \App\Entities\User\Provider\Specialist $provider
 * @property-read \App\Entities\Review\Review $review
 * @property-read Collection|\App\Entities\Data\Score[] $scores what provider did well
 * @mixin \Eloquent
 */
class ProviderReview extends Model
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
     * 1 to 1 with review
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function review()
    {
        return $this->belongsTo(Review::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function provider()
    {
        return $this->belongsTo(Specialist::class, 'provider_id', 'user_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function scores()
    {
        return $this->belongsToMany(
            Score::class,
            'provider_reviews_scores',
            'provider_review_id',
            'score_id'
        );
    }
}
