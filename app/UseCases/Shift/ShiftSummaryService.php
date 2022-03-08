<?php

declare(strict_types=1);

namespace App\UseCases\Shift;

use App\Entities\Shift\Shift;
use Carbon\Carbon;

/**
 * Class ShiftSummaryService
 * Prepares data for shifts monthly report.
 *
 * @package App\UseCases\Shift
 */
class ShiftSummaryService
{
    /**
     * @return Shift[]
     */
    public function getShiftsForPrevMonth()
    {
        $endDate = Carbon::now();
        //$endDate = Carbon::createFromTimeString('2020-01-09 17:15');
        $startDate = Carbon::now()->subMonthNoOverflow();
        $shifts = Shift::where('end_date', '>=', $startDate)
            ->where('end_date', '<', $endDate)
            ->where(function ($query) {
                $query->where('status', Shift::STATUS_FINISHED)
                    ->orWhere(function ($query) {
                        $query->where('status', Shift::STATUS_ACCEPTED_BY_PROVIDER)
                            ->where('processed', true);
                    });
            })

            ->get();

        return $shifts;
    }

    /**
     * @return Shift[]
     */
    public function getShiftsGroupedByPractice()
    {
        return $this->getShiftsForPrevMonth()->groupBy('practice_id');
    }

    /**
     * @return Shift[]
     */
    public function getShiftsGroupedByProvider()
    {
        return $this->getShiftsForPrevMonth()->groupBy('provider_id');
    }
}
