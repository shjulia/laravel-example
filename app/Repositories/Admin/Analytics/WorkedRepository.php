<?php

namespace App\Repositories\Admin\Analytics;

use App\Entities\Shift\Shift;
use App\Entities\Shift\ShiftLog;
use Illuminate\Support\Facades\Cache;

/**
 * Class WorkedRepository
 * @package App\Repositories\Admin\Analytics
 */
class WorkedRepository
{
    /**
     * @return float
     */
    public function getTotalHours(): float
    {
        $value = Cache::store('database')->get('getTotalHours');
        if ($value) {
            return $value / 60;
        }
        $minutes = Shift::finished()
            ->whereHas('creator', function ($query) {
                $query->where('is_test_account', 0)
                    ->active();
            })
            ->sum('shift_time');
        Cache::store('database')->put('getTotalHours', $minutes, 240);
        return $minutes / 60;
    }

    /**
     * @param null|string $startDate
     * @param null|string $endDate
     * @param int|null $position
     * @return array
     */
    public function getHoursByDays(?string $startDate, ?string $endDate, ?int $position): array
    {
        $value = null;
        if (!$startDate && !$endDate) {
            $value = Cache::store('database')->get('getHoursByDays');
        }
        if ($value) {
            return $value;
        }
        $shifts = Shift::finished()
            ->real();
        if ($position) {
            $shifts->where('position_id', $position);
        }
        if ($startDate && $endDate) {
            $shifts->where('end_date', '>=', $startDate)
                ->where('end_date', '<=', $endDate);
        }
        $shifts = $shifts->where('end_date', '!=', null)->get();
        $res = [['Date', 'Hours']];
        foreach ($shifts->groupBy('end_date')->sortKeys() as $shiftGroup) {
            $res[] = [ $shiftGroup[0]->end_date, $shiftGroup->sum('shift_time') / 60];
        }
        if (!$startDate && !$endDate) {
            Cache::store('database')->put('getHoursByDays', $res, 240);
        }
        return $res;
    }

    /**
     * @param string|null $startDate
     * @param string|null $endDate
     * @return array
     */
    public function findCancellationReasons(?string $startDate, ?string $endDate): array
    {
        $value = null;
        if (!$startDate && !$endDate) {
            $value = Cache::store('database')->get('findCancellationReasons');
        }
        if ($value) {
            return $value;
        }
        /** @var Shift[] $shifts */
        $shifts = Shift::whereIn('status', [Shift::STATUS_CANCELED, Shift::STATUS_CANCELED_BY_PRACTICE])
            ->real();
        if ($startDate && $endDate) {
            $shifts->where('end_date', '>=', $startDate)
                ->where('end_date', '<=', $endDate);
        }
        $shifts = $shifts->getModels();
        $reasons = [];
        foreach ($shifts as $shift) {
            $reason = $shift->isCanceledByPracticeStatus()
                ? ($shift->cancellation_reason ?: 'canceled by practice')
                : 'canceled by SA';
            $reasons[$reason] =  isset($reasons[$reason]) ? $reasons[$reason] + 1 : 1;
        }
        $res = [['Cancellation Reason', 'Amount']];
        foreach ($reasons as $key => $value) {
            $res[] = [$key, $value];
        }
        if (!$startDate && !$endDate) {
            Cache::store('database')->put('findCancellationReasons', $res, 240);
        }
        return $res;
    }

    /**
     * @return float
     */
    public function matchingTime(): float
    {
        $value = Cache::store('database')->get('matchingTime');
        if ($value) {
            return $value;
        }
        $shiftLogs = ShiftLog::whereIn('action', [ShiftLog::MATCHING_STARTED, ShiftLog::PROVIDER_ACCEPTED])
            ->get()->groupBy('shift_id');

        $times = [];
        foreach ($shiftLogs as $log) {
            /** @var ShiftLog|null $start */
            $start = isset($log[0]) && $log[0]->isMatchingStartedRecord() ? $log[0] : null;
            /** @var ShiftLog $start */
            $finish = isset($log[1]) && $log[1]->isMatchingStartedRecord() ? $log[1] : null;
            if (!$start || !$finish) {
                continue;
            }
            $times[] = $finish->created_at->diffInRealMinutes($start->created_at);
        }
        $res = collect($times)->avg() ?: 0;
        Cache::store('database')->put('matchingTime', $res, 240);
        return $res;
    }

    /**
     * @return float
     */
    public function successMatchesPercent(): float
    {
        $value = Cache::store('database')->get('successMatchesPercent');
        if ($value) {
            return $value;
        }
        $success = Shift::real()
            ->whereIn('status', [Shift::STATUS_ACCEPTED_BY_PROVIDER, Shift::STATUS_FINISHED])
            ->count();
        $failed = Shift::real()
            ->whereIn('status', [Shift::STATUS_NO_PROVIDERS_FOUND])
            ->count();
        $res = round($success / (($success + $failed) ?: 1) * 100, 2);
        Cache::store('database')->put('successMatchesPercent', $res, 240);
        return $res;
    }
}
