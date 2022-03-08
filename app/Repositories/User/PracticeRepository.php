<?php

namespace App\Repositories\User;

use App\Entities\Notification\EmailMark;
use App\Entities\User\ApproveLog;
use App\Entities\User\Practice\Practice;
use App\Repositories\Data\Location\AreaRepository;
use Carbon\Carbon;

/**
 * Class PracticeRepository
 * @package App\Repositories\User
 */
class PracticeRepository
{
    /** @var AreaRepository $areaRepository */
    private $areaRepository;

    /**
     * PracticeRepository constructor.
     * @param AreaRepository $areaRepository
     */
    public function __construct(AreaRepository $areaRepository)
    {
        $this->areaRepository = $areaRepository;
    }

    public function getById(int $id): Practice
    {
        if (!$practice = Practice::where('id', $id)->first()) {
            throw new \DomainException('Practice not found');
        }
        return $practice;
    }

    /*public function findPracticeUsers(int $id)
    {
        return Practice::where('id', $id)->first()->users;
    }*/

    /**
     * @param Practice $practice
     * @param null|string $state
     * @param null|string $city
     * @param null|string $zip
     * @throws \Exception
     */
    public function setArea(Practice $practice, ?string $state, ?string $city, ?string $zip)
    {
        try {
            if ($area = $this->areaRepository->findByCityOrZip($state, $city, $zip)) {
                $practice->update(['area_id' => $area->id]);
            }
        } catch (\Throwable $e) {
            throw new \Exception('Something went wrong');
        }
    }

    /**
     * @param Practice $practice
     * @param $amount
     * @throws \Exception
     */
    public function setPaidTotal(Practice $practice, $amount)
    {
        try {
            $practice->update([
                'hires_total' => $practice->hires_total + 1,
                'paid_total' => $practice->paid_total + $amount
            ]);
        } catch (\Throwable $e) {
            throw new \Exception('Updating practice earnings went wrong');
        }
    }

    /**
     * @param int $days
     * @param string $markType
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public function findWithNoShiftAfterDays(int $days, string $markType)
    {
        return Practice::where('approval_status', Practice::STATUS_APPROVED)
            ->whereDoesntHave('shifts')
            ->whereDoesntHave('users', function ($query) use ($markType) {
                $query->whereHas('emailMarks', function ($q) use ($markType) {
                    $q->where('type', $markType);
                });
            })
            ->whereHas('users', function ($query) use ($days) {
                $query->whereHas('approveLogs', function ($q) use ($days) {
                    $date = Carbon::now()->subDays($days);
                    $q->where('status', ApproveLog::CHANGED_TO_APPROVED)
                        ->where('created_at', '<=', $date);
                });
            })
            ->get();
    }

    /**
     * @param int $days
     * @param array $types
     * @return Practice[]|\Illuminate\Database\Eloquent\Collection
     */
    public function findPracticesWhereHasOldShifts(int $days, array $types)
    {
        return Practice::whereHas('shifts')
            ->whereDoesntHave('shifts', function ($query) use ($days) {
                $query->where('end_date', '>', now()->subDays($days)->format('Y-m-d'));
            })
            ->whereDoesntHave('users', function ($query) use ($types) {
                $query->whereHas('emailMarks', function ($query) use ($types) {
                    $query->whereIn('type', $types);
                });
            })->get();
    }

    /**
     * @param int $page
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function findPaginate(int $page)
    {
        return Practice::whereHas(
            'users',
            function ($query) {
                $query->where('is_test_account', 0);
            }
        )
            ->with('users')
            ->paginate(20, null, null, $page);
    }

    public function findAllPaginate(): \Generator
    {
        $page = 1;
        while (true) {
            $practices = $this->findPaginate($page);
            if ($practices->isEmpty()) {
                break;
            }
            foreach ($practices as $practice) {
                yield $practice;
            }
            $page++;
        }
    }
}
