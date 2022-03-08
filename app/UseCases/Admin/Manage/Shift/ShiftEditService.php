<?php

declare(strict_types=1);

namespace App\UseCases\Admin\Manage\Shift;

use App\Entities\Shift\Shift;
use App\Entities\User\User;
use App\Events\Shift\ShiftUpdateEvent;
use App\Http\Requests\Shift\TimeRequest;
use App\Jobs\Shift\BonusAddedJob;
use App\Jobs\Shift\CostChangesJob;
use App\Repositories\Payment\ChargeRepository;
use App\UseCases\Shift\PaymentService;
use App\UseCases\Shift\ShiftPaymentService;
use Illuminate\Support\Facades\DB;

/**
 * Class ShiftEditService
 * Edit shift data: change time, update payments, edit charges and bonuses.
 *
 * @package App\UseCases\Admin\Manage\Shift
 */
class ShiftEditService
{
    /**
     * @var \App\UseCases\Shift\ShiftService
     */
    private $shiftService;
    /**
     * @var ChargeRepository
     */
    private $chargeRepository;
    /**
     * @var ShiftPaymentService
     */
    private $shiftPaymentService;
    /**
     * @var PaymentService
     */
    private $paymentService;

    /**
     * ShiftEditService constructor.
     * @param \App\UseCases\Shift\ShiftService $shiftService
     * @param ShiftPaymentService $shiftPaymentService
     * @param PaymentService $paymentService
     * @param ChargeRepository $chargeRepository
     */
    public function __construct(
        \App\UseCases\Shift\ShiftService $shiftService,
        ShiftPaymentService $shiftPaymentService,
        PaymentService $paymentService,
        ChargeRepository $chargeRepository
    ) {
        $this->shiftService = $shiftService;
        $this->chargeRepository = $chargeRepository;
        $this->shiftPaymentService = $shiftPaymentService;
        $this->paymentService = $paymentService;
    }

    /**
     * @param Shift $shift
     * @param TimeRequest $request
     * @param User $admin
     * @throws \Exception
     */
    public function changeTime(Shift $shift, TimeRequest $request, User $admin): void
    {
        if ($shift->isCanceledStatus() || $shift->isArchived()) {
            throw new \DomainException("You can't change time for this shift.");
        }
        if (!$shift->isCompleted() && !$shift->isFinishedStatus()) {
            $this->shiftService->setTime($shift, $request, $admin);
            return;
        }
        $this->shiftService->updateDateTimeValues($shift, $request);
        $oldCosts = ['cost' => $shift->cost, 'costForPractice' => $shift->cost_for_practice, 'time' => $shift->time];
        $costs = $this->shiftService->updateCosts($shift, false);
        $shift->refresh();
        $this->updatePracticePayments($shift, $costs, $oldCosts);
        $this->updateProviderPayments($shift, $costs, $oldCosts);
        CostChangesJob::dispatch($shift, $oldCosts);
    }

    /**
     * @param Shift $shift
     * @param array $costs
     * @param array $oldCosts
     */
    private function updatePracticePayments(Shift $shift, array $costs, array $oldCosts): void
    {
        $costForPractice = $costs['costForPractice'] - $oldCosts['costForPractice'];
        try {
            if ($costForPractice > 0) {
                $this->shiftPaymentService->createCharge(
                    $shift,
                    $costForPractice,
                    true,
                    false
                );
            } elseif ($costForPractice < 0) {
                $lastCharge = $this->chargeRepository->getLastCharge($shift);
                if (!$lastCharge) {
                    throw new \DomainException('Charge already refunded');
                }
                $this->shiftPaymentService->refund($lastCharge, $costForPractice * (-1));
            }
            $shift->practice->update([
                'paid_total' => $shift->practice->paid_total + $costForPractice
            ]);
        } catch (\Exception $e) {
            \LogHelper::error($e);
            throw new \DomainException($e->getMessage());
        }
    }

    /**
     * @param Shift $shift
     * @param array $costs
     * @param array $oldCosts
     */
    private function updateProviderPayments(Shift $shift, array $costs, array $oldCosts): void
    {
        $costForProvider = $costs['cost'] - $oldCosts['cost'];
        $user = $shift->provider->user;
        try {
            if ($costForProvider > 0) {
                $this->paymentService->replenish(
                    $user,
                    $costForProvider,
                    'Extra money for shift #' . $shift->id . ' time changing'
                );
                if ($shift->provider->isExpeditedPaymentStatus()) {
                    $this->paymentService->withdraw($user, $costForProvider, true, true);
                }
            } elseif ($costForProvider < 0) {
                $this->paymentService->replenish(
                    $user,
                    $costForProvider,
                    'Debt for shift #' . $shift->id . ' time changing'
                );
            }
            $shift->provider->update([
                'paid_total' => $shift->provider->paid_total + $costForProvider,
                'hours_total' => $shift->provider->hours_total + round(($shift->shift_time - $oldCosts['time']) / 60)
            ]);
        } catch (\Exception $e) {
            \LogHelper::error($e);
            throw new \DomainException($e->getMessage());
        }
    }

    /**
     * @param Shift $shift
     * @param float $bonus
     * @param User $admin
     * @throws \Exception
     */
    public function changeBonus(Shift $shift, float $bonus, User $admin): void
    {
        if ($shift->bonus == $bonus) {
            throw new \DomainException('Bonus already the same');
        }
        DB::beginTransaction();
        try {
            $shift->changeBonus($bonus);
            $shift->saveOrFail();
            if ($shift->multi_days) {
                /** @var Shift $child */
                foreach ($shift->children as $child) {
                    $child->changeBonus(round($bonus / $shift->multi_days, 2));
                    $child->saveOrFail();
                }
            }
            BonusAddedJob::dispatch($shift);
            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            throw new \DomainException('Bonus saving error');
        }
        event(new ShiftUpdateEvent(
            $shift,
            'Bonus' . $bonus . ' added',
            $admin
        ));
    }
}
