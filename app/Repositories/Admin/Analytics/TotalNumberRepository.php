<?php

namespace App\Repositories\Admin\Analytics;

use App\Entities\User\ApproveLog;
use App\Entities\User\Practice\Practice;
use App\Entities\User\Provider\Specialist;
use App\Entities\User\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;

class TotalNumberRepository
{
    /**
     * @param string|null $startDate
     * @param string|null $endDate
     * @return array
     */
    public function getTotalNumber(?string $startDate, ?string $endDate)
    {
        $value = null;
        if (!$startDate && !$endDate) {
            $value = Cache::store('database')->get('getTotalNumber');
        }
        if ($value) {
            return $value;
        }
        $providers = $this->getProviders($startDate, $endDate);
        $providersNumber = $this->getProvidersNumber($startDate, $endDate);
        $dataProviders = $this->datesAndNumbers($providers, $providersNumber);

        $practices = $this->getPractices($startDate, $endDate);
        $practicesNumber = $this->getPracticesNumber($startDate, $endDate);
        $dataPractices = $this->datesAndNumbers($practices, $practicesNumber);

        $dates = array_unique(array_merge(array_keys($dataProviders), array_keys($dataPractices)));
        sort($dates);

        $allDateNumber = [
            ['Date', 'Providers', 'Practices']
        ];

        foreach ($dates as $date) {
            $providersNumber = $dataProviders[$date] ?? $providersNumber;
            $practicesNumber = $dataPractices[$date] ?? $practicesNumber;
            $allDateNumber[] = [$date, $providersNumber, $practicesNumber];
        }
        if (!$startDate && !$endDate) {
            Cache::store('database')->put('getTotalNumber', $allDateNumber, 240);
        }
        return $allDateNumber;
    }

    /**
     * @param string|null $startDate
     * @param string|null $endDate
     * @return Specialist[]|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    private function getProviders(?string $startDate, ?string $endDate)
    {
        return Specialist::with('user')->whereHas(
            'user',
            function ($query) use ($startDate, $endDate) {
                $query->where('is_test_account', 0)
                    ->active();
                if ($startDate && $endDate) {
                    $query->where([
                        ['created_at', '>=', $startDate],
                        ['created_at', '<=', $endDate]
                    ]);
                }
            }
        )->get();
    }

    /**
     * @param string|null $startDate
     * @param string|null $endDate
     * @return int
     */
    private function getProvidersNumber(?string $startDate, ?string $endDate)
    {
        $providersNumber = 0;
        if (!$startDate || !$endDate) {
            return $providersNumber;
        }
        $providersNumber = Specialist::with('user')
            ->whereHas('user', function ($query) use ($startDate, $endDate) {
                $query->where('is_test_account', 0)
                    ->active()
                    ->where([
                    ['created_at', '<', $startDate]
                ]);
            })->count();

        return $providersNumber;
    }

    /**
     * @param string|null $startDate
     * @param string|null $endDate
     * @return Practice[]|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    private function getPractices(?string $startDate, ?string $endDate)
    {
        return Practice::with('users')
            ->whereHas('users', function ($query) use ($startDate, $endDate) {
                $query->where('user_practice.is_creator', 1)
                    ->where('is_test_account', 0)
                    ->active();
                if ($startDate && $endDate) {
                    $query->where([
                        ['created_at', '>=', $startDate],
                        ['created_at', '<=', $endDate]
                    ]);
                }
            })
            ->withCount('addresses')
            ->get();
    }

    /**
     * @param string|null $startDate
     * @param string|null $endDate
     * @return int
     */
    private function getPracticesNumber(?string $startDate, ?string $endDate)
    {
        $practicesNumber = 0;
        if (!$startDate || !$endDate) {
            return $practicesNumber;
        }
        $practices = Practice::with('users')
            ->whereHas('users', function ($query) use ($startDate, $endDate) {
                $query->where('user_practice.is_creator', 1)
                    ->where('is_test_account', 0)
                    ->active()
                    ->where([
                        ['created_at', '<', $startDate]
                    ]);
            })
            ->withCount('addresses')
            ->get();
        foreach ($practices as $practice) {
            $practicesNumber += $practice->addresses_count;
        }
        $practicesNumber += $practices->count();

        return $practicesNumber;
    }

    /**
     * @param Collection $collection
     * @param int $number
     * @return array
     */
    private function datesAndNumbers(Collection $collection, int $number)
    {
        $data = [];
        foreach ($collection->groupBy('create_date') as $collectionGroup) {
            $number += $collectionGroup->count();
            foreach ($collectionGroup as $practice) {
                $number += $practice->addresses_count ?? 0;
            }
            $data[$collectionGroup[0]->create_date] = $number;
        }

        return $data;
    }

    /**
     * @return float
     */
    public function findRejectedToApprovedRatio(): float
    {
        $value = Cache::store('database')->get('findRejectedToApprovedRatio');
        if ($value) {
            return $value;
        }
        $rejected = User::where('is_rejected', 1)
            ->where('is_test_account', 0)
            ->count();
        $approved = User::where('is_rejected', 0)
            ->where('is_test_account', 0)
            ->where(function ($query) {
                $query->whereHas('practices', function ($query) {
                    $query->where('approval_status', Practice::STATUS_APPROVED);
                })->orWhereHas('specialist', function ($query) {
                    $query->where('approval_status', Specialist::STATUS_APPROVED);
                });
            })
            ->count();
        $res = round(!$approved ? 0 : $rejected / $approved, 2) * 100;
        Cache::store('database')->put('findRejectedToApprovedRatio', $res, 240);
        return $res;
    }

    /**
     * @return float
     */
    public function avgTimeToApproval(): float
    {
        $value = Cache::store('database')->get('avgTimeToApproval');
        if ($value) {
            return $value;
        }
        $users = User::whereNull('signup_step')
            ->with(['approveLogs' => function ($query) {
                $query->whereIn('desc', [
                    ApproveLog::REGISTRATION_FINISHED_PROVIDER,
                    ApproveLog::REGISTRATION_FINISHED_PRACTICE
                ])
                    ->orWhere('status', ApproveLog::CHANGED_TO_APPROVED);
            }])
            ->get();

        $times = [];
        foreach ($users as $user) {
            $finished = null;
            $approved = null;
            /** @var ApproveLog $record */
            foreach ($user->approveLogs as $record) {
                if ($record->isFinishedStep()) {
                    $finished = $record->created_at;
                }
                if ($record->isApproved()) {
                    $approved = $record->created_at;
                }
            }
            if (!$finished || !$approved) {
                 continue;
            }
            $times[] = $approved->diffInRealMinutes($finished);
        }
        $res = round(collect($times)->avg() / 60, 2);
        Cache::store('database')->put('avgTimeToApproval', $res, 240);
        return $res;
    }

    /**
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function findApprovalLogs()
    {
        return User::whereNull('signup_step')
            ->whereHas('approveLogs', function ($query) {
                $query->whereIn('desc', [
                    ApproveLog::REGISTRATION_FINISHED_PROVIDER,
                    ApproveLog::REGISTRATION_FINISHED_PRACTICE
                ]);
            })
            ->whereHas('approveLogs', function ($query) {
                $query->where('status', ApproveLog::CHANGED_TO_APPROVED);
            })
            ->with(['approveLogs' => function ($query) {
                $query->whereIn('desc', [
                    ApproveLog::REGISTRATION_FINISHED_PROVIDER,
                    ApproveLog::REGISTRATION_FINISHED_PRACTICE
                ])
                    ->orWhere('status', ApproveLog::CHANGED_TO_APPROVED);
            }])
            ->distinct()
            ->paginate();
    }

    /**
     * @return float
     */
    public function avgTimeToComplete(): float
    {
        $value = Cache::store('database')->get('avgTimeToComplete');
        if ($value) {
            return $value;
        }
        $users = User::whereNull('signup_step')
            ->with(['approveLogs' => function ($query) {
                $query->whereIn('desc', [
                    ApproveLog::REGISTRATION_FINISHED_PROVIDER,
                    ApproveLog::REGISTRATION_FINISHED_PRACTICE
                ]);
            }])
            ->get();
        $times = [];
        foreach ($users as $user) {
            $finishedAt = $user->approveLogs[0] ?? null;
            if (!$finishedAt) {
                continue;
            }
            $times[] = $finishedAt->created_at->diffInRealMinutes($user->created_at);
        }
        $res = round(collect($times)->avg() / 60, 2);
        Cache::store('database')->put('avgTimeToComplete', $res, 240);
        return $res;
    }

    /**
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|User[]
     */
    public function timesToComplete()
    {
        return User::whereNull('signup_step')
            ->whereHas('approveLogs', function ($query) {
                $query->whereIn('desc', [
                    ApproveLog::REGISTRATION_FINISHED_PROVIDER,
                    ApproveLog::REGISTRATION_FINISHED_PRACTICE
                ]);
            })
            ->with(['approveLogs' => function ($query) {
                $query->whereIn('desc', [
                    ApproveLog::REGISTRATION_FINISHED_PROVIDER,
                    ApproveLog::REGISTRATION_FINISHED_PRACTICE
                ]);
            }])
            ->paginate();
    }
}
