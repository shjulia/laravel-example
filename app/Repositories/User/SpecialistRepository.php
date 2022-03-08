<?php

namespace App\Repositories\User;

use App\Entities\Notification\EmailMark;
use App\Entities\Shift\ShiftInvite;
use App\Entities\User\Provider\Specialist;
use App\Entities\User\User;
use App\Repositories\Data\Location\AreaRepository;

/**
 * Class SpecialistRepository
 * @package App\Repositories\User
 */
class SpecialistRepository
{
    /**
     * @var AreaRepository
     */
    private $areaRepository;

    /**
     * SpecialistRepository constructor.
     * @param AreaRepository $areaRepository
     */
    public function __construct(AreaRepository $areaRepository)
    {
        $this->areaRepository = $areaRepository;
    }

    /**
     * @param int $id
     * @return Specialist
     */
    public function getById(int $id): Specialist
    {
        $provider = Specialist::where('user_id', $id)->first();
        if (!$provider) {
            throw new \DomainException('Provider not found');
        }
        return $provider;
    }

    public function getByUuid(string $uuid): Specialist
    {
        $provider = Specialist::whereHas('user', function ($query) use ($uuid) {
            $query->where('uuid', $uuid);
        })->first();
        if (!$provider) {
            throw new \DomainException('Provider not found');
        }
        return $provider;
    }

    /**
     * @param User $user
     * @param bool $formated
     * @return Specialist
     */
    public function getAdditionalDataByUser(User $user, bool $formated = true)
    {
        $specialist = Specialist::where('user_id', $user->id)
            ->with(['specialities', 'availabilities', 'holidays'])
            ->first();

        if (!$formated) {
            return $specialist;
        }
        return $specialist->setAppends(['additional', 'photo_url']);
    }

    /**
     * @param Specialist $specialist
     * @return Specialist
     */
    public function getWithUser(Specialist $specialist): Specialist
    {
        return Specialist::where('user_id', $specialist->user_id)
            ->with(['user', 'position'])
            ->first()
            ;//->setAppends(['photo_url']);
    }

    /**
     * @param string $id
     * @return Specialist
     */
    public function getSpecialistByCheckrCandidate(string $id)
    {
        $specialist = Specialist::whereHas('checkr', function ($query) use ($id) {
                $query->where('checkr_candidate_id', $id);
        })->first();
        if (!$specialist) {
            throw new \DomainException('Specialist not found');
        }
        return $specialist;
    }

    /**
     * @param string $dln
     * @return Specialist|null
     */
    public function findProviderByDLNumber(string $dln): ?Specialist
    {
        return Specialist::where('driver_license_number', $dln)->first();
    }

    /**
     * @param Specialist $specialist
     * @param null|string $state
     * @param null|string $city
     * @param null|string $zip
     * @throws \Exception
     */
    public function setArea(Specialist $specialist, ?string $state, ?string $city, ?string $zip)
    {
        try {
            if ($area = $this->areaRepository->findByCityOrZip($state, $city, $zip)) {
                $specialist->update(['area_id' => $area->id]);
            } else {
                $specialist->update(['area_id' => null]);
            }
        } catch (\Throwable $e) {
            throw new \Exception('Something went wrong');
        }
    }

    /**
     * @param Specialist $specialist
     * @param float $amount
     * @param float $shiftTime
     */
    public function setPaidTotal(Specialist $specialist, float $amount, float $shiftTime)
    {
        $specialist->update([
            'paid_total' => $specialist->paid_total + $amount,
            'hours_total' => $specialist->hours_total + $shiftTime,
            'jobs_total' => $specialist->jobs_total + 1,
        ]);
    }

    public function getNotAvailable()
    {
        return Specialist::where('approval_status', Specialist::STATUS_APPROVED)
            ->where(function ($query) {
                $query->where('available', 0)
                    ->orWhereDoesntHave('availabilities');
            })
            ->leftJoin('users', 'specialists.user_id', '=', 'users.id')
            ->where('users.is_test_account', 0)
            ->where('users.is_rejected', 0)
            ->get();
    }

    /**
     * @param Specialist $specialist
     */
    public function save(Specialist $specialist): void
    {
        try {
            $specialist->saveOrFail();
        } catch (\Throwable $e) {
            throw new \DomainException('Provider saving error');
        }
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function findWhereALotOfNoRespondShifts()
    {
        return Specialist::whereDoesntHave('shiftInvites', function ($query) {
                $query->where('status', ShiftInvite::ACCEPTED)
                    ->orWhere('status', ShiftInvite::DECLINED);
        })
            ->withCount(['shiftInvites' => function ($query) {
                $query->where('status', ShiftInvite::NO_RESPOND);
            }])
            ->has('shiftInvites', '>=', 3)
            ->whereDoesntHave('user', function ($query) {
                $query->whereHas('emailMarks', function ($q) {
                    $q->where('type', EmailMark::MISSING_OUT_SHIFTS);
                });
            })
            ->with('shiftInvites.shift')
            ->get();
    }

    /**
     * @param int $page
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|Specialist[]
     */
    public function findPaginate(int $page)
    {
        return Specialist::whereHas(
            'user',
            function ($query) {
                $query->where('is_test_account', 0);
            }
        )
            ->with('user')
            ->paginate(20, null, null, $page);
    }

    public function findAllPaginate(): \Generator
    {
        $page = 1;
        while (true) {
            $providers = $this->findPaginate($page);
            if ($providers->isEmpty()) {
                break;
            }
            foreach ($providers as $provider) {
                yield $provider;
            }
            $page++;
        }
    }
}
