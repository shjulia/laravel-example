<?php

namespace App\Repositories\Shift;

use App\Entities\Shift\Shift;
use App\Entities\Shift\ShiftInvite;
use App\Entities\Shift\ShiftTracking;
use App\Entities\User\Practice\Practice;
use App\Entities\User\Provider\Specialist;
use Carbon\Carbon;

/**
 * Class ShiftRepository
 * @package App\Repositories\Shift
 */
class ShiftRepository
{
    /**
     * @param int $practiceId
     * @param string|null $tz
     * @return Shift[]|\Illuminate\Database\Eloquent\Collection
     */
    public function findFutureShiftsByPractice(int $practiceId, ?string $tz = null)
    {
        return Shift::where(['practice_id' => $practiceId])
            ->whereIn('status', [
                Shift::STATUS_MATCHING,
                Shift::STATUS_PARENT_MATCHING,
                Shift::STATUS_ACCEPTED_BY_PROVIDER,
                Shift::STATUS_WAITING,
                Shift::STATUS_NO_PROVIDERS_FOUND
            ])
            ->where(function ($query) use ($tz) {
                $date = Carbon::now($tz)->format('Y-m-d');
                ;
                $query->where('date', '>', $date)
                ->orWhere(function ($query) use ($date, $tz) {
                    $nowTime = Carbon::now($tz)->format('H:i');
                    $query->where('date', $date)
                        ->where('from_time', '>', $nowTime);
                });
            })
            //->whereHas('provider')
            ->orderBy('date', 'DESC')
            ->with(['provider.user', 'position'])
            ->get();
    }

    /**
     * @param int $practiceId
     * @param array $futureShiftsIds
     * @return Shift[]
     */
    public function findProgressShiftsByPractice(int $practiceId, array $futureShiftsIds)
    {
        return Shift::where(['practice_id' => $practiceId])
            ->whereNotIn('status', [
                Shift::STATUS_FINISHED,
                Shift::STATUS_CANCELED,
                Shift::STATUS_CANCELED_BY_PRACTICE,
                Shift::STATUS_NO_PROVIDERS_FOUND,
                Shift::STATUS_ARCHIVED
            ])
            ->whereNotIn('id', $futureShiftsIds)
            ->orderBy('date', 'DESC')
            ->with(['provider.user', 'position'])
            ->get();
    }

    /**
     * @param Specialist $provider
     * @return Shift[]
     */
    public function findShiftsByProvider(Specialist $provider)
    {
        $shifts =  Shift::where(['provider_id' => $provider->user_id])
            ->where('status', Shift::STATUS_ACCEPTED_BY_PROVIDER)
            ->where('date', '>', Carbon::yesterday($provider->user->tz)->format('Y-m-d'))
            ->orderBy('date', 'DESC')
            ->with(['practice', 'position', 'reviews.practiceReview'])
            ->paginate();
        $shifts->each(function ($shift) {
            $shift->setAppends(['practice_location']);
            $shift->practice->setAppends(['practice_photo_url']);
        });
        return $shifts;
    }

    /**
     * @param int $id
     * @param int $providerId
     * @return Shift|null
     */
    public function findShiftByProviderAndId(int $id, int $providerId): ?Shift
    {
        $shift = Shift::where(['id' => $id, 'provider_id' => $providerId])
            ->with(['practice', 'position', 'reviews.practiceReview'])
            ->first();
        if ($shift) {
            $shift->setAppends(['practice_location', 'is_started_by_provider']);
            $shift->practice->setAppends(['practice_photo_url']);
        }
        return $shift;
    }

    /**
     * @param bool|null $isWithTest
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function findAll(?bool $isWithTest = true)
    {
        $shifts = Shift::select('shifts.*')
            ->orderBy('shifts.id', 'DESC')
            ->with(['provider.user', 'practice', 'position'])
            ->where('shifts.status', "!=", Shift::STATUS_ARCHIVED)
            ->where('parent_shift_id', null);
        if (!$isWithTest) {
            $shifts->leftJoin('users', 'shifts.creator_id', '=', 'users.id')
                ->where('users.is_test_account', 0);
        }
        $shifts = $shifts->paginate();
        return $shifts;
    }

    /**
     * @param bool|null $isWithTest
     * @return Shift|\Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Builder
     */
    public function findAllArchived(?bool $isWithTest = true)
    {
        $shifts = Shift::select('shifts.*')
            ->orderBy('shifts.id', 'DESC')
            ->with(['provider.user', 'practice', 'position'])
            ->where('shifts.status', Shift::STATUS_ARCHIVED);
        if (!$isWithTest) {
            $shifts->leftJoin('users', 'shifts.creator_id', '=', 'users.id')
                ->where('users.is_test_account', 0);
        }
        $shifts = $shifts->paginate();
        return $shifts;
    }

    /**
     * @param int $id
     * @return Shift
     */
    public function getById(int $id): Shift
    {
        if (
            !$shift = Shift::where('id', $id)
            ->with(['provider', 'practice', 'position'/*, 'steps'*/, 'reviewFromProvider', 'reviewFromPractice'])
            ->first()
        ) {
            throw new \DomainException('Not found');
        }
        $shift->setAppends(['practice_location']);
        return $shift;
    }

    /**
     * @param int $id
     * @return Shift
     */
    public function getByIdOnlyShift(int $id): Shift
    {
        if (
            !$shift = Shift::where('id', $id)
            ->first()
        ) {
            throw new \DomainException('Not found');
        }
        return $shift;
    }

    /**
     * @param int $parentId
     * @param string $date
     * @return Shift|null
     */
    public function findChildShiftByParentAndDate(int $parentId, string $date): ?Shift
    {
        return Shift::where('parent_shift_id', $parentId)
            ->where('date', $date)
            ->with('')
            ->first();
    }

    /**
     * @param int $id
     * @return bool|int
     */
    public function checkAcceptByProvider(int $id)
    {
        /** @var Shift $shift */
        $shift = Shift::where(['id' => $id])->first();
        if ($shift->isAcceptedByProviderStatus()) {
            return true;
        }
        if ($shift->isDeclinedStatus()) {
            return -1;
        }
        return false;
    }

    /**
     * @param Shift $shift
     * @return bool
     */
    public function findInTheSameTime(Shift $shift): bool
    {
        return Shift::where([
            'practice_id' => $shift->practice_id,
            'date' => $shift->date,
            'from_time' => $shift->from_time,
            'to_time' => $shift->to_time
        ])
            ->where('id', '!=', $shift->id)
            ->where('status', '!=', Shift::STATUS_CANCELED)
            ->where('status', '!=', Shift::STATUS_CANCELED_BY_PRACTICE)
            ->exists();
    }

    /**
     * @param Shift $shift
     * @param int $providerId
     * @return ShiftInvite
     */
    public function getShiftInvite(Shift $shift, int $providerId): ShiftInvite
    {
        $invite = $shift->shiftInvites()->where('provider_id', $providerId)->first();
        if (!$invite) {
            throw new \DomainException('Shift invite not found');
        }
        return $invite;
    }

    /**
     * @param Shift $shift
     * @return array
     */
    public function getExcludedProviders(Shift $shift): array
    {
        return $shift->shiftInvites()
            ->pluck('provider_id')
            ->toArray();
    }

    /**
     * @param int $couponId
     * @return int
     */
    public function findCouponUsagesAmount(int $couponId): int
    {
        return Shift::where('coupon_id', $couponId)->count();
    }

    /**
     * @param int $couponId
     * @param int $practiceId
     * @return int
     */
    public function findCouponUsagesAmountByPractice(int $couponId, int $practiceId): int
    {
        return Shift::where('coupon_id', $couponId)
            ->where('practice_id', $practiceId)
            ->count();
    }

    /**
     * @param Shift $shift
     * @return ShiftTracking|null
     */
    public function findStartTrack(Shift $shift): ?ShiftTracking
    {
        return ShiftTracking::where('shift_id', $shift->id)
            ->where('action', ShiftTracking::ACTION_STARTED)
            ->first();
    }

    /**
     * @param Shift $shift
     */
    public function save(Shift $shift): void
    {
        try {
            $shift->saveOrFail();
        } catch (\Throwable $e) {
            throw new \DomainException('Shift saving error');
        }
    }

    /**
     * @param Practice $practice
     * @param Shift $shift
     * @return bool
     */
    public function isFirstShiftForPractice(Practice $practice, Shift $shift): bool
    {
        return !Shift::where('practice_id', $practice->id)
            ->where('id', '!=', $shift->id)
            ->whereNull('parent_shift_id')
            ->exists();
    }

    /**
     * @param Specialist $provider
     * @param Shift $shift
     * @return bool
     */
    public function isFirstShiftForProvider(Specialist $provider, Shift $shift): bool
    {
        return !Shift::where('provider_id', $provider->id)
            ->where('id', '!=', $shift->id)
            ->exists();
    }
}
