<?php

declare(strict_types=1);

namespace App\UseCases\Admin\Manage\Data\Review;

use App\Entities\Data\Score;
use App\Http\Requests\Admin\Data\Review\Score\CreateRequest;
use App\Http\Requests\Admin\Data\Review\Score\EditRequest;
use App\Repositories\Shift\Review\ScoresRepository;

/**
 * Class ScoreService
 * Manage scores.
 *
 * @package App\UseCases\Admin\Manage\Data\Review
 */
class ScoresService
{
    /**
     * @var ScoresRepository
     */
    private $scoresRepository;

    /**
     * ScoresService constructor.
     * @param ScoresRepository $scoresRepository
     */
    public function __construct(ScoresRepository $scoresRepository)
    {
        $this->scoresRepository = $scoresRepository;
    }

    /**
     * @param CreateRequest $request
     * @return Score
     */
    public function create(CreateRequest $request): Score
    {
        try {
            $score = Score::create([
                'title' => $request->title,
                'for_type' => $request->for_type,
                'active' => (bool)$request->active
            ]);
        } catch (\Exception $e) {
            throw new \DomainException('Creating error');
        }
        return $score;
    }

    /**
     * @param Score $score
     * @param EditRequest $request
     * @return Score
     */
    public function edit(Score $score, EditRequest $request): Score
    {
        $score =  $this->scoresRepository->getById($score->id);
        try {
            $score->update([
                'title' => $request->title,
                'for_type' => $request->for_type,
                'active' => (bool)$request->active
            ]);
        } catch (\Exception $e) {
            throw new \DomainException('Updating error');
        }
        return $score;
    }

    /**
     * @param Score $score
     */
    public function destroy(Score $score): void
    {
        $score =  $this->scoresRepository->getById($score->id);
        try {
            $score->delete();
        } catch (\Exception $e) {
            throw new \DomainException('Deleting error');
        }
    }
}
