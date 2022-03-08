<?php

namespace App\Repositories\Shift\Review;

use App\Entities\Data\Score;

/**
 * Class ScoresRepository
 * @package App\Repositories\Shift\Review
 */
class ScoresRepository
{
    /**
     * @return Score[]
     */
    public function findAll()
    {
        return Score::orderBy('id', 'DESC')->paginate();
    }

    /**
     * @param int $id
     * @return Score
     */
    public function getById(int $id): Score
    {
        if (!$score = Score::where('id', $id)->first()) {
            throw new \DomainException('Score bubble not found');
        }
        return $score;
    }
}
