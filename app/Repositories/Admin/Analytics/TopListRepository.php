<?php

namespace App\Repositories\Admin\Analytics;

use App\Entities\User\Practice\Practice;
use App\Entities\User\Provider\Specialist;
use Illuminate\Support\Facades\Cache;

/**
 * Class TopListRepository
 * @package App\Repositories\Admin\Analytics
 */
class TopListRepository
{
    /**
     * @return array
     */
    public function getPractices()
    {
        $value = Cache::store('database')->get('getPracticesTop');
        if ($value) {
            return $value;
        }
        $practices = Practice::with('finishedShifts')
            ->whereHas('users', function ($query) {
                $query->where('is_test_account', 0)
                    ->active();
            })
            ->where('paid_total', '>', 0)
            ->orderBy('paid_total', 'desc')
            ->limit(10)
            ->get();
        $practiceData = [
            ['Practice Name', 'Total hires', 'Total paid']
        ];

        foreach ($practices as $practice) {
            $practiceData[] = [$practice->practice_name, $practice->hires_total, $practice->paid_total];
        }
        Cache::store('database')->put('getPracticesTop', $practiceData, 240);
        return $practiceData;
    }

    /**
     * @return array
     */
    public function getProviders()
    {
        $value = Cache::store('database')->get('getProvidersTop');
        if ($value) {
            return $value;
        }
        $providers = Specialist::whereHas('user', function ($query) {
                $query->where('is_test_account', 0)
                    ->active();
        })
            ->where('paid_total', '>', 0)
            ->orderBy('paid_total', 'desc')
            ->limit(10)
            ->with('user')
            ->get();
        $providerData = [
            ['Provider Name', 'Hours worked', 'Total paid']
        ];
        foreach ($providers as $provider) {
            $providerData[] = [$provider->user->full_name, $provider->hours_total, $provider->paid_total];
        }
        Cache::store('database')->put('getProvidersTop', $providerData, 240);
        return $providerData;
    }

    /**
     * @param bool $isTop
     * @return array
     */
    public function ratedPractices(bool $isTop)
    {
        $value = Cache::store('database')->get('ratedPractices' . $isTop);
        if ($value) {
            return $value;
        }
        $practices = Practice::whereHas('users', function ($query) {
                $query->where('is_test_account', 0)
                    ->active();
        })
            ->where('average_stars', '!=', 0)
            ->orderBy('average_stars', $isTop ? 'desc' : 'asc')
            ->limit(10)
            ->get();
        $practiceData = [
            ['Practice Name', 'Average Rate']
        ];

        foreach ($practices as $practice) {
            $practiceData[] = [$practice->practice_name, $practice->average_stars];
        }
        Cache::store('database')->put('ratedPractices' . $isTop, $practiceData, 240);
        return $practiceData;
    }

    /**
     * @param bool $isTop
     * @return array
     */
    public function ratedProviders(bool $isTop)
    {
        $value = Cache::store('database')->get('ratedProviders' . $isTop);
        if ($value) {
            return $value;
        }
        $providers = Specialist::whereHas('user', function ($query) {
            $query->where('is_test_account', 0)
                ->active();
        })
            ->where('average_stars', '!=', 0)
            ->orderBy('average_stars', $isTop ? 'desc' : 'asc')
            ->with('user')
            ->limit(10)
            ->get();
        $providerData = [
            ['Provider Name', 'Average Rate']
        ];
        foreach ($providers as $provider) {
            $providerData[] = [$provider->user->full_name, $provider->average_stars];
        }
        Cache::store('database')->put('ratedProviders' . $isTop, $providerData, 240);
        return $providerData;
    }
}
