<?php

namespace App\Repositories\Admin\Analytics;

use App\Entities\Shift\Shift;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

/**
 * Class ProfitRepository
 * @package App\Repositories\Admin\Analytics
 */
class ProfitRepository
{
    /**
     * @param null|string $startDate
     * @param null|string $endDate
     * @return array
     */
    public function getEarns(?string $startDate, ?string $endDate): array
    {
        $value = null;
        if (!$startDate && !$endDate) {
            $value = Cache::store('database')->get('getEarns');
        }
        if ($value) {
            return $value;
        }
        $query = Shift::finished()
            ->where('multi_days', 0)
            ->real();
        if ($startDate && $endDate) {
            $query->where('end_date', '>=', $startDate)
                ->where('end_date', '<=', $endDate);
        }
        $total = (clone $query)->sum('cost');
        $withProfit = $query->sum('cost_for_practice');
        $profit = $withProfit - $total;
        $res = [
            ['Element', 'Total Revenue', 'Profit'],
            ['', ['v' => $withProfit, 'f' => '$' . $withProfit], ['v' => $profit, 'f' => '$' . $profit]]
        ];
        if (!$startDate && !$endDate) {
            Cache::store('database')->put('getEarns', $res, 240);
        }
        return $res;
    }

    /**
     * @return array
     */
    public function getEarnsByMonths(): array
    {
        $value = Cache::store('database')->get('getEarnsByMonths');
        if ($value) {
            return $value;
        }
        $shiftsGroup = Shift::finished()
            ->real()
            ->where('multi_days', 0)
            ->orderBy('end_date')
            ->get()
            ->groupBy(function ($item, $key) {
                return substr($item->end_date, 0, 7);
            });
        $result = [];
        $result[] = ['Month', 'Total Revenue', 'Profit'];
        foreach ($shiftsGroup as $date => $shifts) {
            $total = $shifts->sum('cost');
            $withProfit =  $shifts->sum('cost_for_practice');
            $profit = $withProfit - $total;
            $row = [
                ['v' => $date, 'f' => Carbon::createFromFormat('Y-m', $date)->format('M, Y')],
                ['v' => $withProfit, 'f' => ('$' . $withProfit . ' (profit: $' . $profit . ')')],
                ['v' => $profit, 'f' => '$' . $profit]
            ];
            $result[] = $row;
        }
        Cache::store('database')->put('getEarnsByMonths', $result, 240);
        return $result;
    }

    /**
     * @return array
     */
    public function findFutureByMonths(): array
    {
        $value = Cache::store('database')->get('findFutureByMonths');
        if ($value) {
            return $value;
        }
        $shiftsGroup = Shift::where('status', Shift::STATUS_ACCEPTED_BY_PROVIDER)
            ->where('processed', 0)
            ->where('multi_days', 0)
            ->where('end_date', '>=', now()->subDays(1)->format('Y-m-d'))
            ->whereHas('creator', function ($query) {
                $query->where('is_test_account', 0);
            })
            ->orderBy('end_date')
            ->get()
            ->groupBy(function ($item, $key) {
                return substr($item->end_date, 0, 7);
            });
        $result = [];
        $result[] = ['Month', 'Total Revenue', 'Profit', '# of shifts'];
        foreach ($shiftsGroup as $date => $shifts) {
            $amount = $shifts->count();
            $total = $shifts->sum('cost');
            $withProfit =  $shifts->sum('cost_for_practice');
            $profit = $withProfit - $total;
            $row = [
                ['v' => $date, 'f' => Carbon::createFromFormat('Y-m', $date)->format('M, Y')],
                ['v' => $withProfit, 'f' => '$' . $withProfit],
                ['v' => $profit, 'f' => '$' . $profit],
                ['v' => $amount * 1000, 'f' => $amount]
            ];
            $result[] = $row;
        }
        Cache::store('database')->put('findFutureByMonths', $result, 240);
        return $result;
    }
}
