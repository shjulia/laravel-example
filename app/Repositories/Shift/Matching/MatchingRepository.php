<?php

namespace App\Repositories\Shift\Matching;

use App\Entities\Data\Holiday;
use App\Entities\Shift\Shift;
use App\Entities\User\Practice\Practice;
use App\Entities\User\Provider\Specialist;
use App\Entities\User\User;
use App\Helpers\HolidaysHelper;
use Illuminate\Support\Facades\DB;

/**
 * Class MatchingRepository
 * @package App\Repositories\Shift\Matching
 */
class MatchingRepository
{
    /**
     * @param int $positionId
     * @param string $state
     * @param array $excludes
     * @param bool|null $isTest
     * @return array
     */
    public function getBaseFoundProviders(int $positionId, string $state, array $excludes, ?bool $isTest = false): array
    {
        return DB::table('specialists')
            ->select('user_id')
            ->leftJoin('users', 'specialists.user_id', '=', 'users.id')
            //->where('users.status', User::ACTIVE)
            ->where('approval_status', Specialist::STATUS_APPROVED)
            ->where('available', 1)
            ->where('position_id', $positionId)
            ->where('driver_state', $state)
            ->whereNotIn('user_id', $excludes)
            ->where('users.is_test_account', $isTest)
            ->where('users.is_rejected', 0)
            ->pluck('user_id')
            ->toArray();
    }

    /**
     * @param int $positionId
     * @param string $state
     * @param array $excludes
     * @param bool|null $isTest
     * @return array
     */
    public function getBaseFoundProvidersWithoutAvailability(
        int $positionId,
        string $state,
        array $excludes,
        ?bool $isTest = false
    ): array {
        return DB::table('specialists')
            ->select('user_id')
            ->leftJoin('users', 'specialists.user_id', '=', 'users.id')
            ->where('approval_status', Specialist::STATUS_APPROVED)
            ->where('position_id', $positionId)
            ->where('driver_state', $state)
            ->whereNotIn('user_id', $excludes)
            ->where('users.is_test_account', $isTest)
            ->where('users.is_rejected', 0)
            ->pluck('user_id')
            ->toArray();
    }

    /**
     * @param array $providers
     * @param Shift $shift
     * @return array
     */
    public function getByHired(array $providers, Shift $shift): array
    {
        $bad =  DB::table('shifts')
            ->select('provider_id')
            ->where('position_id', $shift->position_id)
            //->where('date', $this->shift->date)
            ->where(function ($query) use ($shift) {
                $query->where([
                    ['from_time', '>=', $shift->from_time],
                    ['from_time', '<=', $shift->to_time]
                ])
                    ->orWhere([
                        ['to_time', '>=', $shift->from_time],
                        ['to_time', '<=', $shift->to_time]
                    ])
                    ->orWhere([
                        ['from_time', '<=', $shift->from_time],
                        ['to_time', '>=', $shift->to_time]
                    ]);
            })
            ->where(function ($query) use ($shift) {
                $query->where([
                    ['date', '>=', $shift->date],
                    ['date', '<=', $shift->end_date]
                ])
                    ->orWhere([
                        ['end_date', '>=', $shift->date],
                        ['end_date', '<=', $shift->end_date]
                    ])
                    ->orWhere([
                        ['date', '<=', $shift->date],
                        ['end_date', '>=', $shift->end_date]
                    ]);
            })
            /*->where('from_time', '>=', $this->shift->from_time)
            ->where('to_time', '<=', $this->shift->to_time)*/
            ->where('status', Shift::STATUS_ACCEPTED_BY_PROVIDER)
            ->whereIn('provider_id', $providers)
            ->pluck('provider_id')
            ->toArray();
        return array_values(array_diff($providers, $bad));
    }

    /**
     * @param array $providers
     * @param Shift $shift
     * @return array
     */
    public function getByHolidays(array $providers, Shift $shift): array
    {
        $holidays = new HolidaysHelper(date('Y-m-d'));
        $dates = $holidays->getOnlyDates();
        if (!in_array($shift->date, $dates)) {
            return $providers;
        }
        $holiday = Holiday::where('title', $holidays->getHolidayNameByDate($shift->date))->first();
        if (!$holiday) {
            return $providers;
        }
        return Specialist::select('user_id')
            ->whereIn('user_id', $providers)
            ->whereHas('holidays', function ($query) use ($holiday) {
                $query->where('holidays.id', $holiday->id);
            })
            ->pluck('user_id')
            ->toArray();
    }

    /**
     * @param array $providers
     * @param float $hourRate
     * @return array
     */
    public function getByRate(array $providers, float $hourRate): array
    {
        return Specialist::select('user_id')
            ->whereIn('user_id', $providers)
            ->where(function ($query) use ($hourRate) {
                $query->where('min_rate', '<=', $hourRate)
                    ->orWhere('min_rate', null);
            })
            ->pluck('user_id')
            ->toArray();
    }

    /**
     * @param array $providers
     * @param Shift $shift
     * @return array
     */
    public function getByAvailabilities(array $providers, Shift $shift): array
    {
        $days = $this->getWeeksDaysByDates($shift->date, $shift->end_date);
        return Specialist::select('user_id')
            ->whereIn('user_id', $providers)
            ->where(function ($query) use ($days, $shift) {
                $query->where(function ($query) use ($days, $shift) {
                    if ($shift->from_time > $shift->to_time) {
                        //so "to_time" is time in the next day and this is overnight request
                        unset($days[count($days) - 1]); //remove last day
                        foreach ($days as $day) {
                            $query->whereHas('availabilities', function ($query) use ($day, $shift) {
                                $query->where('day', $day)
                                    ->where('from_hour', '<=', $shift->from_time)
                                    ->where('to_hour', '00:00');
                            })
                                ->whereHas('availabilities', function ($query) use ($day, $shift) {
                                    $query->where('day', $day == 7 ? 0 : $day + 1)//next day
                                    ->where('from_hour', '00:00')
                                        ->where('to_hour', '>=', $shift->to_time);
                                });
                        }
                    } else {
                        foreach ($days as $day) {
                            $query->orWhereHas('availabilities', function ($query) use ($day, $shift) {
                                $query->where('day', $day)
                                    ->where('from_hour', '<=', $shift->from_time)
                                    ->where('to_hour', '>=', $shift->to_time);
                            });
                        }
                    }
                })
                    /*->orWhereDoesntHave('availabilities', function ($query) use ($days) {
                        $query->whereIn('day', $days);
                    })*/;
            })
            ->pluck('user_id')
            ->toArray();
    }

    /**
     * @param string $startDate
     * @param string $endDate
     * @return array
     */
    private function getWeeksDaysByDates(string $startDate, string $endDate): array
    {
        $weekDayStart = date('w', strtotime($startDate));
        $weekDayEnd = date('w', strtotime($endDate));
        $weekDayStart = (int)$weekDayStart > 0 ? (int)$weekDayStart : 7;
        $weekDayEnd = (int)$weekDayEnd > 0 ? (int)$weekDayEnd : 7;
        $daysCount = (strtotime($endDate) - strtotime($startDate)) / (60 * 60 * 24);

        if ($daysCount >= 7) {
            return [1, 2, 3, 4, 5, 6, 7];
        }

        $days = [];
        while ($weekDayStart != $weekDayEnd) {
            if ($weekDayStart == 7) {
                $days[] = $weekDayStart;
                $weekDayStart = 1;
                continue;
            }
            $days[] = $weekDayStart;
            $weekDayStart++;
        }
        $days[] = $weekDayEnd;
        return $days;
    }

    /**
     * @param array $providers
     * @param int $practiceId
     * @return array
     */
    public function getByProviderReviews(array $providers, int $practiceId): array
    {
        $bad = DB::table('provider_reviews')
            ->select('provider_id')
            ->leftJoin('reviews', 'provider_reviews.review_id', '=', 'reviews.id')
            ->whereIn('provider_id', $providers)
            ->where('from_practice_id', $practiceId)
            ->where('reviews.score', '<=', 3)
            ->pluck('provider_id')
            ->toArray();
        return array_values(array_diff($providers, $bad));
    }

    /**
     * @param array $providers
     * @param int $practiceId
     * @return array
     */
    public function getByPracticeReviews(array $providers, int $practiceId): array
    {
        $bad = DB::table('practice_reviews')
            ->select('from_provider_id')
            ->leftJoin('reviews', 'practice_reviews.review_id', '=', 'reviews.id')
            ->whereIn('from_provider_id', $providers)
            ->where('practice_id', $practiceId)
            ->where('reviews.score', '<=', 3)
            ->pluck('from_provider_id')
            ->toArray();

        return array_values(array_diff($providers, $bad));
    }

    /**
     * @param array $providers
     * @param float $averageStarsToProvider
     * @return array
     */
    public function getByAverage(array $providers, float $averageStarsToProvider): array
    {
        return DB::table('specialists')
            ->select('user_id')
            ->whereIn('user_id', $providers)
            ->where(function ($query) use ($averageStarsToProvider) {
                $query->where('average_stars', '>', $averageStarsToProvider)
                    ->orWhere('jobs_total', 0)
                    ->orWhere('average_stars', 5);
            })
            ->pluck('user_id')
            ->toArray();
    }

    /**
     * @param array $providers
     * @param Practice $practice
     * @return array
     */
    public function getByArea(array $providers, Practice $practice): array
    {
        return DB::table('specialists')
                ->select('user_id')
                ->whereIn('user_id', $providers)
                ->where('area_id', $practice->area_id)
                ->pluck('user_id')
                ->toArray();
    }

    /**
     * @param array $providers
     * @param string $zip
     * @return array
     */
    public function getByZip(array $providers, string $zip): array
    {
        return DB::table('specialists')
            ->select('user_id')
            ->whereIn('user_id', $providers)
            ->where('driver_zip', $zip)
            ->pluck('user_id')
            ->toArray();
    }

    /**
     * @param array $providers
     * @param string $city
     * @return array
     */
    public function getByCity(array $providers, string $city): array
    {
        return DB::table('specialists')
            ->select('user_id')
            ->whereIn('user_id', $providers)
            ->where('driver_city', $city)
            ->pluck('user_id')
            ->toArray();
    }

    /**
     * @param array $providers
     * @param string $state
     * @return array
     */
    public function getByLicenceState(array $providers, string $state): array
    {
        return Specialist::select('user_id')
            ->whereIn('user_id', $providers)
            ->whereHas('licenses', function ($query) use ($state) {
                $query->where('state', $state);
            })
            ->pluck('user_id')
            ->toArray();
    }

    /**
     * @param int $positionId
     * @param string $state
     * @param array $excludes
     * @return array
     */
    public function getBaseFoundNotAvailabelProviders(int $positionId, string $state, array $excludes): array
    {
        return DB::table('specialists')
            ->select('user_id')
            ->leftJoin('users', 'specialists.user_id', '=', 'users.id')
            //->where('users.status', User::ACTIVE)
            ->where('approval_status', Specialist::STATUS_APPROVED)
            ->where('available', 0)
            ->where('position_id', $positionId)
            ->where('driver_state', $state)
            ->whereNotIn('user_id', $excludes)
            ->where('users.is_test_account', 0)
            ->where('users.is_rejected', 0)
            ->pluck('user_id')
            ->toArray();
    }

    /**
     * @param array $providers
     * @param array $tasks
     * @return array
     */
    public function getByTasks(array $providers, array $tasks): array
    {
        return Specialist::select('user_id')
            ->whereIn('user_id', $providers)
            ->whereHas('routineTasks', function ($query) use ($tasks) {
                $query->whereIn('task_id', $tasks);
            })
            ->pluck('user_id')
            ->toArray();
    }
}
