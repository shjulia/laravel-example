<?php

declare(strict_types=1);

namespace App\UseCases\Shift;

use App\Entities\Shift\Coupon;
use App\Entities\Shift\Shift;
use App\Entities\Shift\ShiftInvite;
use App\Entities\Shift\ShiftLog;
use App\Entities\User\Practice\Practice;
use App\Entities\User\User;
use App\Events\Shift\AcceptShiftEvent;
use App\Events\Shift\ProvidersNotFoundEvent;
use App\Events\Shift\ShiftCanceledEvent;
use App\Events\Shift\ShiftUpdateEvent;
use App\Exceptions\Shift\NoProvidersAreAvailableException;
use App\Http\Requests\Shift\LocationRequest;
use App\Http\Requests\Shift\ShiftBaseRequest;
use App\Http\Requests\Shift\TasksRequest;
use App\Http\Requests\Shift\TimeRequest;
use App\Jobs\Shift\CostChangesJob;
use App\Jobs\Shift\FirstProviderRequestJob;
use App\Jobs\Shift\MatchNowJob;
use App\Repositories\Industry\PositionRepository;
use App\Repositories\Payment\ChargeRepository;
use App\Repositories\Shift\ShiftRepository;
use Carbon\Carbon;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Support\Facades\DB;

/**
 * Class ShiftService
 * Shift creation: set location, time and calculate costs.
 * Prepares for matching and start matching.
 * Check and apply Coupon.
 * Cancel shift, calculate cancellation fee and do refund.
 *
 * @package App\UseCases\Shift
 */
class ShiftService
{
    /**
     * @var ShiftRepository
     */
    private $shiftRepository;

    /**
     * @var PositionRepository
     */
    private $positionRepository;

    /**
     * @var MatchingService
     */
    private $matchingService;

    /** @var CostService */
    private $costService;

    /** @var ChargeRepository $chargeRepository */
    private $chargeRepository;
    /**
     * @var Dispatcher
     */
    private $dispatcher;

    /** @var int  */
    public const CANCELLATION_FEE = 50;
    /**
     * @var ShiftPaymentService
     */
    private $shiftPaymentService;

    /**
     * ShiftService constructor.
     * @param ShiftRepository $shiftRepository
     * @param PositionRepository $positionRepository
     * @param MatchingService $matchingService
     * @param CostService $costService
     * @param ChargeRepository $chargeRepository
     * @param Dispatcher $dispatcher
     * @param ShiftPaymentService $shiftPaymentService
     */
    public function __construct(
        ShiftRepository $shiftRepository,
        PositionRepository $positionRepository,
        MatchingService $matchingService,
        CostService $costService,
        ChargeRepository $chargeRepository,
        Dispatcher $dispatcher,
        ShiftPaymentService $shiftPaymentService
    ) {
        $this->shiftRepository = $shiftRepository;
        $this->positionRepository = $positionRepository;
        $this->matchingService = $matchingService;
        $this->costService = $costService;
        $this->chargeRepository = $chargeRepository;
        $this->dispatcher = $dispatcher;
        $this->shiftPaymentService = $shiftPaymentService;
    }

    /**
     * @param Practice $practice
     * @param User $user
     * @param ShiftBaseRequest $request
     * @param bool $isNow
     * @param Shift|null $shift
     * @return Shift
     */
    public function createBase(
        Practice $practice,
        User $user,
        ShiftBaseRequest $request,
        bool $isNow = false,
        ?Shift $shift = null
    ): Shift {
        $position = $this->positionRepository->getById((int)$request->position);
        if (!$shift) {
            $shift = Shift::createBase($position, $user, $practice);
            $action = 'Shift started creating for ' . $position->title . ' position';
        } else {
            $shift->editPosition($position);
            $action = 'Position changed to ' . $position->title;
        }
        if ($isNow) {
            $shift->setNowDate();
        }
        if (!$shift->save()) {
            throw new \DomainException('Saving error');
        }
        $this->dispatcher->dispatch(new ShiftUpdateEvent(
            $shift,
            $action,
            $user,
            true
        ));
        return $shift;
    }

    /**
     * @param Shift $shift
     * @param LocationRequest $request
     */
    public function setLocation(Shift $shift, LocationRequest $request): void
    {
        if (!$request->location) {
            return;
        }
        $location = $shift->practice->addresses()->where('id', $request->location)->first();
        if (!$location) {
            throw new \DomainException('Location not found');
        }
        try {
            $shift->update([
                'location_id' => $location->id
            ]);
            $this->dispatcher->dispatch(new ShiftUpdateEvent(
                $shift,
                'Location set to ' . $location->full_address,
                $shift->creator
            ));
        } catch (\Exception $e) {
            throw new \DomainException('Location setting error');
        }
    }

    /**
     * @param Shift $shift
     * @param TimeRequest $request
     */
    public function updateDateTimeValues(Shift $shift, TimeRequest $request): void
    {
        $shift->editDateTimeValues(
            $request->start_date ?: $shift->date,
            $request->end_date,
            $request->time_from,
            $request->time_to,
            $request->shift_time,
            $request->multi_days ?: 0,
            $request->lunch_break ? (int)$request->lunch_break : 0
        );
        if (!$shift->save()) {
            throw new \DomainException('Shift saving error');
        }
    }

    /**
     * @param Shift $shift
     * @param TimeRequest $request
     * @param User|null $admin
     * @throws \Exception
     */
    public function setTime(Shift $shift, TimeRequest $request, ?User $admin = null): void
    {
        DB::beginTransaction();
        try {
            $this->updateDateTimeValues($shift, $request);
            $this->dispatcher->dispatch(new ShiftUpdateEvent(
                $shift,
                'Shift time set to ' . $shift->period(),
                $admin ?: $shift->creator
            ));
            $oldCosts = ['cost' => $shift->cost, 'costForPractice' => $shift->cost_for_practice];

            $costs = $this->updateCosts($shift);
            if (!$shift->multi_days) {
                $this->shiftPaymentService->refundAndPay($shift, $costs['costForPractice']);
            }

            if ($admin) {
                CostChangesJob::dispatch($shift, $oldCosts);
            }

            DB::commit();
        } catch (\DomainException $e) {
            DB::rollBack();
            \LogHelper::error($e, ['userId' => $shift->creator->id]);
            throw $e;
        } catch (\Exception $e) {
            DB::rollBack();
            \LogHelper::error($e, ['userId' => $shift->creator->id]);
            throw new \DomainException('Time saving error');
        }
    }

    /**
     * @param Shift $shift
     * @param bool|null $calculateSurge
     * @return array
     */
    public function updateCosts(Shift $shift, ?bool $calculateSurge = true): array
    {
        $costs = $this->costService->getCosts($shift, $calculateSurge);
        $shift->editCosts($costs['cost'], $costs['costForPractice']);
        if (!$shift->save()) {
            throw new \DomainException('Shift saving error');
        }
        if ($coupon = $shift->coupon) {
            $shift->refresh();
            $shift->applyCoupon($coupon);
            $shift->save();
            $costs['costForPractice'] = $shift->cost_for_practice;
        }

        $this->dispatcher->dispatch(new ShiftUpdateEvent(
            $shift,
            'Price calculated: for provider - $' . $costs['cost'] . ' | for practice - $'
            . $costs['costForPractice'],
            null
        ));

        return $costs;
    }

    /**
     * @param Shift $shift
     * @param TasksRequest $request
     * @throws \Exception
     */
    public function setTasks(Shift $shift, TasksRequest $request): void
    {
        $shift->update([
           'tasks' => $request->settasks ? $request->tasks : []
        ]);
        if ($shift->multi_days) {
            $this->createSubShiftsForMultipleShift($shift);
        }

        FirstProviderRequestJob::dispatch($shift);
        if ($request->settasks) {
            $this->dispatcher->dispatch(new ShiftUpdateEvent(
                $shift,
                'Routine tasks set ',
                $shift->creator
            ));
        }
    }

    /**
     * @param Shift $shift
     * @throws \Exception
     */
    private function createSubShiftsForMultipleShift(Shift $shift): void
    {
        $date = $shift->date;
        DB::beginTransaction();
        try {
            while (true) {
                $child = $this->shiftRepository->findChildShiftByParentAndDate($shift->id, $date);
                if (!$child) {
                    $child = Shift::copyParentToChild($shift, $date);
                    $child->saveOrFail();
                }
                $this->shiftPaymentService->refundAndPay($child, $child->cost_for_practice);
                if ($date == $shift->end_date) {
                    break;
                }
                $date = Carbon::createFromFormat('Y-m-d', $date)->addDay()->format('Y-m-d');
            }
            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            \LogHelper::error($e);
            throw new \DomainException('Problem with creation multi-day shift. Check you card balance.');
        }
    }

    /**
     * Prepares shifts fot matching: set matching status and launch matching job.
     *
     * @param Shift $shift
     */
    public function startMatching(Shift $shift): void
    {
        if ($shift->isMatchingStatus() || $shift->isParentMatchingStatus()) {
            return;
        }
        //$this->dispatcher->dispatch(new ProviderRequestedEvent($shift));
        /*if ($shift->date >
            Carbon::now($shift->creator->tz)->addDays(6)->format('Y-m-d')
        ) {
            $shift->setWaitingStatus();
            $shift->save();
            return;
        }*/
        $shift->setMatchingStatus();
        $shift->save();
        $this->dispatcher->dispatch(new ShiftUpdateEvent(
            $shift,
            ShiftLog::MATCHING_STARTED,
            null,
            true
        ));
        MatchNowJob::dispatch($shift);
    }

    /**
     * Matching: finds suitable providers for shift.
     *
     * @param Shift $shift
     * @return array
     * @throws NoProvidersAreAvailableException
     */
    public function match(Shift $shift): array
    {
        try {
            $providerData = $this->matchingService->match($shift);
        } catch (NoProvidersAreAvailableException $e) {
            //$this->refundCharge($shift);
            $shift->setPotentialProvider();
            $shift->save();
            throw new NoProvidersAreAvailableException($e->getMessage());
        }

        $shift->setPotentialProvider($providerData['user_id'], $providerData['distanceVal']);
        $shift->save();
        try {
            $invite = ShiftInvite::newInvite($shift, $shift->potentialProvider);
            $invite->saveOrFail();
        } catch (\Throwable $e) {
            \LogHelper::error($e, ['message' => 'Can\'t invite provider to the shift' . $shift->id]);
            return $providerData;
        }

        if ($shift->potential_provider_id) {
            $this->dispatcher->dispatch(
                new AcceptShiftEvent($shift->potentialProvider->user, $shift->setRelations([]))
            );
            $this->dispatcher->dispatch(new ShiftUpdateEvent(
                $shift,
                'Provider ' . $shift->potential_provider_id
                . '(' . $shift->potentialProvider->user->full_name  . ') invited',
                null
            ));
        }
        return $providerData;
    }

    /**
     * @param Shift $shift
     * @param string|null $reason
     * @param bool $isRematch
     */
    public function cancel(Shift $shift, ?string $reason, bool $isRematch): void
    {
        /*if ($shift->provider_id) {
            throw new \DomainException('You can\'t cancel shift. Provider has already accepted invitation.');
        }*/
        if ($shift->isCanceledStatus()) {
            throw new \DomainException('Shift have been already canceled');
        }
        try {
            $shift->setCanceledByPracticeStatus();
            if (!$isRematch && $chargeId = $this->calculateCancellationFee($shift)) {
                $shift->setCancellationFee($chargeId, self::CANCELLATION_FEE);
            }
            $shift->setCancellationReason($reason);
            $shift->save();
            if ($shift->isHasProvider()) {
                $this->dispatcher->dispatch(new ShiftCanceledEvent($shift));
            }
            if (!$shift->multi_days) {
                $this->refundCharge($shift);
            }
            if ($shift->multi_days) {
                /** @var Shift $child */
                foreach ($shift->children as $child) {
                    $child->setCancellationReason($reason);
                    $child->save();
                    $this->refundCharge($child);
                    if ($child->isHasProvider()) {
                        $this->dispatcher->dispatch(new ShiftCanceledEvent($child));
                    }
                }
            }
            $this->dispatcher->dispatch(new ShiftUpdateEvent(
                $shift,
                'Practice canceled shift. Reason: ' . ($reason ?: '') . '. Cancellation fee: $'
                . (isset($chargeId) ? self::CANCELLATION_FEE : 0),
                $shift->creator,
                true
            ));
        } catch (\Exception $e) {
            \LogHelper::error($e, ['userId' => $shift->creator->id]);
            throw new \DomainException('Canceling error');
        }
    }

    /**
     * @param Shift $shift
     * @return string|null
     * @throws \Exception
     */
    private function calculateCancellationFee(Shift $shift): ?string
    {
        if (!$shift->isHasProvider() && !$shift->isHasProviderInChildren()) {
            return null;
        }
        $createdP10 = Carbon::createFromTimeString($shift->created_at)
            ->addMinutes(10)
            ->format('Y-m-d H:i:s');
        if ($createdP10 > Carbon::now()->format('Y-m-d H:i:s')) {
            return null;
        }
        $nowP24 = Carbon::now($shift->creator->tz)->addHour(24)->format('Y-m-d H:i:s');

        if ($nowP24 < ($shift->date . ' ' . $shift->from_time . ':00')) {
            return null;
        }
        return $this->shiftPaymentService->createChargeForCancellationFee($shift, self::CANCELLATION_FEE);
    }

    /**
     * @param Shift $shift
     * @param Coupon $coupon
     * @return Shift
     */
    public function applyCoupon(Shift $shift, Coupon $coupon): Shift
    {
        $this->checkCoupon($shift, $coupon);
        $shift->applyCoupon($coupon);
        try {
            $shift->saveOrFail();
            $this->dispatcher->dispatch(new ShiftUpdateEvent(
                $shift,
                'Practice applied coupon: ' . $coupon->id,
                $shift->creator
            ));
            if (!$shift->multi_days) {
                $this->shiftPaymentService->refundAndPay($shift, $shift->cost_for_practice);
                $this->dispatcher->dispatch(new ShiftUpdateEvent(
                    $shift,
                    'Charge for shift created and frizzed. Sum - ' . $shift->cost_for_practice,
                    null
                ));
            }
            /** @var Shift $child */
            foreach ($shift->children as $child) {
                $child->applyCoupon($coupon);
                $child->saveOrFail();
                $this->shiftPaymentService->refundAndPay($child, $child->cost_for_practice);
                $this->dispatcher->dispatch(new ShiftUpdateEvent(
                    $child,
                    'Charge for shift created and frizzed. Sum - ' . $child->cost_for_practice,
                    null
                ));
            }
        } catch (\Throwable $e) {
            throw new \DomainException('Coupon applying error');
        }
        return $shift;
    }

    /**
     * @param Shift $shift
     * @param Coupon $coupon
     */
    private function checkCoupon(Shift $shift, Coupon $coupon): void
    {
        if ($shift->isChild()) {
            throw new \DomainException('You can\'t use coupon to this shift');
        }
        if ($shift->coupon_id) {
            throw new \DomainException('You have already used coupon for this shift.');
        }
        $coupon->checkValidByTime($shift->creator->tz);
        $coupon->checkValidByBill($shift->cost_for_practice);
        if ($coupon->use_globally_limit) {
            $count = $this->shiftRepository->findCouponUsagesAmount($coupon->id);
            $coupon->checkValidByGloballyLimit($count);
        }
        if ($coupon->use_per_account_limit) {
            $count = $this->shiftRepository->findCouponUsagesAmountByPractice($coupon->id, $shift->practice_id);
            $coupon->checkValidByAccountLimit($count);
        }
        $coupon->checkValidByPosition($shift->position_id);
        $coupon->checkValidByState($shift->practice->state);
    }

    /**
     * @param Shift $shift
     */
    public function noProvidersInTime(Shift $shift): void
    {
        $shift->setNoProvidersFoundStatus();
        $shift->save();
        $this->dispatcher->dispatch(new ProvidersNotFoundEvent($shift));
        $this->dispatcher->dispatch(new ShiftUpdateEvent(
            $shift,
            'There are no providers found in time. ',
            null,
            true
        ));
        //$this->refundCharge($shift);
    }

    /**
     * @param Shift $shift
     * @param float|null $sum
     */
    public function refundCharge(Shift $shift, ?float $sum = null): void
    {
        $charge = $this->chargeRepository->getLastCharge($shift);
        if (!$charge) {
            return;
        }
        try {
            $this->shiftPaymentService->refund($charge, $sum);
            $this->dispatcher->dispatch(new ShiftUpdateEvent(
                $shift,
                'Charge for shift refunded. ' . $charge->charge_stripe_id,
                null
            ));
        } catch (\Exception $e) {
            \LogHelper::error($e, ['userId' => $shift->creator->id]);
            throw new \DomainException($e->getMessage());
        }
    }

    /**
     * @param Shift $shift
     */
    public function findNewProvider(Shift $shift): void
    {
        if (
            $shift->startsInHours() <= Shift::MIN_TIME_BEFORE_SHIFT
            && ($shift->shift_time - Shift::HOUR < Shift::MIN_SHIFT_TIME)
        ) {
            $this->noProvidersInTime($shift);
            return;
        }

        $shift->recalculateFloating();
        $shift->save();

        $this->updateCosts($shift, false);
        $this->startMatching($shift);
    }
}
