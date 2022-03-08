<?php

namespace App\Repositories\Statistics;

use App\Entities\Statistics\MatchingSteps;

/**
 * Class MatchingStepsRepository
 * @package App\Repositories\Statistics
 */
class MatchingStepsRepository
{
    /**
     * @param int $shiftId
     * @param int $try
     * @param string $title
     * @param array $data
     */
    public function createStep(int $shiftId, int $try, string $title, array $data): void
    {
        MatchingSteps::create([
            'shift_id' => $shiftId,
            'try' => $try,
            'title' => $title,
            'data' => json_encode($data)
        ]);
    }

    /**
     * @param int $shiftId
     * @return MatchingSteps
     */
    public function getForShift(int $shiftId): MatchingSteps
    {
        return MatchingSteps::where('shift_id', $shiftId)->orderBy('id')->get();
    }

    /**
     * @param int $shiftId
     * @return int
     */
    public function findNexStep(int $shiftId): int
    {
        $step = MatchingSteps::where('shift_id', $shiftId)
            ->where('try', '!=', 0)
            ->orderBy('id', 'DESC')
            ->first();
        if (!$step) {
            return 1;
        }
        return $step->try + 1;
    }
}
