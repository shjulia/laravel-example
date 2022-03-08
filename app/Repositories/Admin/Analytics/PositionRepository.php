<?php

namespace App\Repositories\Admin\Analytics;

use App\Entities\Industry\Position;
use App\Entities\Shift\Shift;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * Class ProvidersRepository
 * @package App\Repositories\Admin\Analytics
 */
class PositionRepository
{
    /**
     * @param null|string $startDate
     * @param null|string $endDate
     * @return array
     */
    public function getProvidersWithPosition(?string $startDate, ?string $endDate): array
    {
        $value = null;
        if (!$startDate && !$endDate) {
            $value = Cache::store('database')->get('getProvidersWithPosition');
        }
        if ($value) {
            return $value;
        }
        $positions = Position::withCount([
            'providerUsers' => function ($query) use ($startDate, $endDate) {
                $query->where('is_test_account', 0)
                    ->active();
                if ($startDate && $endDate) {
                    $query->where('created_at', '>=', $startDate)
                        ->where('created_at', '<=', $endDate);
                }
            }])
            ->get();
        $res = $positions->map(function ($position) {
            return [$position->title, $position->provider_users_count];
        });
        $res = array_merge([['Position', 'Providers']], $res->toArray());
        if (!$startDate && !$endDate) {
            Cache::store('database')->put('getProvidersWithPosition', $res, 240);
        }
        return $res;
    }

    /**
     * @param null|string $startDate
     * @param null|string $endDate
     * @return array
     */
    public function getRevenueWithPositions(?string $startDate, ?string $endDate): array
    {
        $value = null;
        if (!$startDate && !$endDate) {
            $value = Cache::store('database')->get('getRevenueWithPositions');
        }
        if ($value) {
            return $value;
        }
        $positions = Position::get();
        $res = [['Position', 'Providers revenue']];
        foreach ($positions as $position) {
            $revenue = $this->getRevenueByPosition($position->id, $startDate, $endDate);
            if (!$revenue) {
                continue;
            }
            $res[] = [$position->title, ['v' => $revenue, 'f' => '$' . $revenue]];
        }
        if (!$startDate && !$endDate) {
            Cache::store('database')->put('getRevenueWithPositions', $res, 240);
        }
        return $res;
    }

    /**
     * @param int $position
     * @param null|string $startDate
     * @param null|string $endDate
     * @return float
     */
    private function getRevenueByPosition(int $position, ?string $startDate, ?string $endDate): float
    {
        $value = null;
        if (!$startDate && !$endDate) {
            $value = Cache::store('database')->get('getRevenueByPosition');
        }
        if ($value) {
            return $value;
        }
        $shifts = Shift::where(['position_id' => $position])
            ->where(function ($query) {
                $query->where('status', Shift::STATUS_FINISHED)
                    ->orWhere([
                        ['status', '=', Shift::STATUS_ACCEPTED_BY_PROVIDER],
                        ['processed', '=', 1]
                    ]);
            })
            ->whereHas('creator', function ($query) {
                $query->where('is_test_account', 0)
                    ->active();
            });
        if ($startDate && $endDate) {
            $shifts->where('created_at', '>=', $startDate)
                ->where('created_at', '<=', $endDate);
        }
        $res = $shifts->sum('cost');
        if (!$startDate && !$endDate) {
            Cache::store('database')->put('getRevenueByPosition', $res, 240);
        }
        return $res;
    }
}
