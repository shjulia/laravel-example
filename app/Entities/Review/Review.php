<?php

declare(strict_types=1);

namespace App\Entities\Review;

use App\Entities\User\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Review - Base review entity for practices and for providers
 *
 * @package App\Entities\Review
 * @property int $id
 * @property int $date
 * @property float $score stars amount (from 1 to 5)
 * @property string|null $text
 * @property string|null $answer
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int $shift_id
 * @property-read \App\Entities\Review\PracticeReview $practiceReview review to practice
 * @property-read \App\Entities\Review\ProviderReview $providerReview review to provider
 * @mixin \Eloquent
 */
class Review extends Model
{
    /**
     * @var array
     */
    protected $guarded = [];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function practiceReview()
    {
        return $this->hasOne(PracticeReview::class, 'review_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function providerReview()
    {
        return $this->hasOne(ProviderReview::class);
    }

    /**
     * @param User $user
     * @return string
     */
    public function getUserTimeReview(User $user)
    {
        return Carbon::createFromTimestamp($this->date, $user->tz)->format('d M, Y');
    }
}
