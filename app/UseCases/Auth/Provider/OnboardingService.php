<?php

declare(strict_types=1);

namespace App\UseCases\Auth\Provider;

use App\Entities\User\Provider\Specialist;
use App\Entities\User\User;
use App\Events\User\ActionLogEvent;
use App\Http\Requests\Auth\Practice\Details\ToolRequest;
use App\Http\Requests\Auth\Provider\Onboarding\AvailabilityRequest;
use App\Http\Requests\Auth\Provider\Onboarding\DistanceRequest;
use App\Http\Requests\Auth\Provider\Onboarding\HolidaysRequest;
use App\Http\Requests\Auth\Provider\Onboarding\LengthRequest;
use App\Http\Requests\Auth\Provider\Onboarding\SpecialitiesRequest;
use App\Http\Requests\Auth\Provider\Onboarding\TasksRequest;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Support\Facades\DB;

/**
 * Class OnboardingService
 * Setting parameters which allow to start working: shift length, rate, distance, routine tasks,
 * Specialities and availability time
 *
 * @package App\UseCases\Auth\Provider
 */
class OnboardingService
{
    /**
     * @var Dispatcher
     */
    private $dispatcher;

    public function __construct(Dispatcher $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    /**
     * @param User $user
     * @param LengthRequest $request
     */
    public function setShiftLength(User $user, LengthRequest $request): void
    {
        $provider = $user->specialist;
        try {
            $provider->update([
                'shift_length_min' => $request->min,
                'shift_length_max' => $request->max
            ]);
            $this->dispatcher->dispatch(new ActionLogEvent($user, 'onboarding Min max shift length updated'));
        } catch (\Exception $e) {
            throw new \DomainException('Shift length saving error');
        }
    }

    /**
     * @param User $user
     * @param float $rate
     * @param User|null $admin
     */
    public function setRate(User $user, float $rate, ?User $admin = null): void
    {
        $provider = $user->specialist;
        try {
            $provider->setMinRate($rate);
            $provider->saveOrFail();
            $this->dispatcher->dispatch(new ActionLogEvent($user, 'onboarding Min rate updated', $admin));
        } catch (\Throwable $e) {
            throw new \DomainException('Shift rate saving error');
        }
    }

    /**
     * @param User $user
     * @param DistanceRequest $request
     */
    public function setDistance(User $user, DistanceRequest $request): void
    {
        $provider = $user->specialist;
        try {
            $provider->update([
                'shift_distance_max' => $request->is_distance ? $request->distance : null,
                'shift_duration_max' => $request->is_duration ? $request->duration : null
            ]);
            $this->dispatcher->dispatch(new ActionLogEvent($user, 'onboarding Distance updated'));
        } catch (\Exception $e) {
            throw new \DomainException('Shift distance saving error');
        }
    }

    /**
     * @param User $user
     * @param TasksRequest $request
     */
    public function setRoutineTasks(User $user, TasksRequest $request): void
    {
        $provider = $user->specialist;
        try {
            $changes = $provider->routineTasks()->sync($request->tasks);
            if (!empty($changes['attached']) || !empty($changes['detached'])) {
                $this->dispatcher->dispatch(new ActionLogEvent($user, 'onboarding Routine tasks updated'));
            }
        } catch (\Exception $e) {
            throw new \DomainException('Shift distance saving error');
        }
    }

    /**
     * @param ToolRequest $request
     * @param User $user
     */
    public function saveTool(ToolRequest $request, User $user): void
    {
        try {
            $provider = $user->specialist;
            $provider->tool_id = $request->tool;
            $provider->saveOrFail();
            $this->dispatcher->dispatch(new ActionLogEvent($user, 'Management software updated'));
        } catch (\Throwable $e) {
            \LogHelper::error($e, ['userId' => $user->id]);
            throw new \DomainException('Practice Management software info saving error');
        }
    }

    /**
     * @param User $user
     * @param SpecialitiesRequest $request
     */
    public function setSpecialities(User $user, SpecialitiesRequest $request): void
    {
        $provider = $user->specialist;
        try {
            $changes = $provider->specialities()->sync($request->specialities ?: []);
            if (!empty($changes['attached']) || !empty($changes['detached'])) {
                $this->dispatcher->dispatch(new ActionLogEvent($user, 'onboarding Specialities updated'));
            }
        } catch (\Exception $e) {
            throw new \DomainException('Shift distance saving error');
        }
    }

    /**
     * @param User $user
     * @param HolidaysRequest $request
     */
    public function setHolidaysAvailability(User $user, HolidaysRequest $request): void
    {
        $provider = $user->specialist;
        try {
            $availabilityHolidays = array_keys($request->holiday ?? []);
            $changes = $provider->holidays()->sync($availabilityHolidays);
            if (!empty($changes['attached']) || !empty($changes['detached'])) {
                $this->dispatcher->dispatch(new ActionLogEvent($user, 'onboarding Holidays updated'));
            }
        } catch (\Exception $e) {
            throw new \DomainException('Shift distance saving error');
        }
    }

    /**
     * @param $user
     * @param AvailabilityRequest $request
     * @throws \Exception
     */
    public function setAvailability($user, AvailabilityRequest $request): void
    {
        /** @var Specialist $provider */
        $provider = $user->specialist;
        DB::beginTransaction();
        try {
            $provider->availabilities()->delete();
            foreach ($request->from ?? [] as $key => $from) {
                if (!isset($request->from[$key]) || !isset($request->to[$key]) || !isset($request->day[$key])) {
                    continue;
                }
                foreach (explode(',', $request->day[$key]) as $day) {
                    if (!in_array($day, [1,2,3,4,5,6,7])) {
                        continue;
                    }
                    $provider->availabilities()->create(
                        [
                            'from_hour' => $request->from[$key],
                            'to_hour' => $request->to[$key],
                            'day' => (int)$day
                        ]
                    );
                }
            }
            if (!$provider->availabilities->isEmpty()) {
                $provider->update(['available' => 1]);
            }
            $this->dispatcher->dispatch(new ActionLogEvent($user, 'onboarding Availability changed'));
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            \LogHelper::error($e, ['userId' => $user->id]);
            throw new \DomainException('Additional data saving error');
        }
    }
}
