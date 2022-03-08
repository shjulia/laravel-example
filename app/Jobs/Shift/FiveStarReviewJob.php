<?php

declare(strict_types=1);

namespace App\Jobs\Shift;

use App\Entities\Review\Review;
use App\Entities\User\User;
use App\Mail\Shift\FiveStarReviewMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class FiveStarReviewJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * @var Review
     */
    private $review;
    /**
     * @var User
     */
    private $user;
    /**
     * @var string
     */
    private $name;

    /**
     * FiveStarReviewJob constructor.
     * @param Review $review
     * @param User $user
     * @param string $name
     */
    public function __construct(Review $review, User $user, string $name)
    {
        $this->review = $review;
        $this->user = $user;
        $this->name = $name;
    }

    public function handle()
    {
        if ($this->review->score != 5) {
            return;
        }

        \Mail::to($this->user->email)->send(
            new FiveStarReviewMail($this->name, $this->review->getUserTimeReview($this->user))
        );
    }
}
