<?php

declare(strict_types=1);

namespace App\UseCases\Shift;

use App\Entities\Payment\Charge;
use App\Entities\Payment\Charge as Payment;
use App\Entities\Shift\Shift;
use App\Events\Shift\ShiftUpdateEvent;
use App\Repositories\Payment\ChargeRepository;
use App\Services\Wallet\Practice\WalletService;
use Carbon\Carbon;
use Illuminate\Contracts\Events\Dispatcher;

/**
 * Class ShiftPaymentService
 * Create and freeze charge for shift and refund if cost for practice is not equal with amount of frozen charge.
 *
 * @package App\UseCases\Shift
 */
class ShiftPaymentService
{
    /**
     * @var ChargeRepository
     */
    private $chargeRepository;
    /**
     * @var Dispatcher
     */
    private $dispatcher;
    /**
     * @var WalletService
     */
    private $walletService;

    public function __construct(
        ChargeRepository $chargeRepository,
        WalletService $walletService,
        Dispatcher $dispatcher
    ) {
        $this->chargeRepository = $chargeRepository;
        $this->dispatcher = $dispatcher;
        $this->walletService = $walletService;
    }

    /**
     * @param Shift $shift
     * @param float $costForPractice
     * @throws \Exception
     */
    public function refundAndPay(Shift $shift, float $costForPractice): void
    {
        $prevCharges = $this->chargeRepository->getNotRefundedCharges($shift);
        $isCharge = false;
        if ($prevCharges->count() > 0) {
            foreach ($prevCharges as $charge) {
                if ($charge->amount != $costForPractice) {
                    $this->refund($charge);
                } else {
                    $isCharge = true;
                }
            }
        }
        if (!$isCharge) {
            $this->createCharge($shift, $costForPractice, false);
        }
    }

    public function refund(Charge $charge, ?float $amount = null): void
    {
        $this->walletService->refund($charge->charge_stripe_id, $amount);
        $charge->update([
            'is_refund' => 1,
            'refund_amount' => $amount ?: $charge->refund_amount
        ]);
        $this->dispatcher->dispatch(new ShiftUpdateEvent(
            $charge->shift,
            '$' . ($amount ?: $charge->refund_amount) . ' for shift was refunded. ' . $charge->charge_stripe_id,
            null
        ));
    }

    /**
     * @param Shift $shift
     * @param float $cost
     * @param bool $capture
     * @param bool $isMain
     */
    public function createCharge(Shift $shift, float $cost, bool $capture = false, bool $isMain = true)
    {
        $chargeId = $this->walletService->createPayment(
            $shift->practice->getWallet()->wallet_client_id,
            $cost,
            $capture
        );
        Payment::create([
            'charge_stripe_id' => $chargeId,
            'shift_id' => $shift->id,
            'practice_id' => $shift->practice_id,
            'amount' => $cost,
            'created' => Carbon::now()->toDateTimeString(),
            'is_capture' => $capture,
            'is_refund' => false,
            'is_main' => $isMain,
            'charge_status' => Payment::CHARGE_STATUS_PENDING
        ]);
        $this->dispatcher->dispatch(new ShiftUpdateEvent(
            $shift,
            'Charge for the shift was created and frozen. Sum - $' . $cost,
            null,
            true
        ));
    }

    public function createChargeForCancellationFee(Shift $shift, float $cost): string
    {
        return $this->walletService->createPayment(
            $shift->practice->getWallet()->wallet_client_id,
            $cost,
            true
        );
    }

    public function captureCharge(Shift $shift): void
    {
        $payment = $this->chargeRepository->getLastCharge($shift);
        if (!$payment) {
            return;
        }
        $this->walletService->capture($payment->charge_stripe_id);
        $payment->update(['is_capture' => true]);
    }

    public function handleStatus(string $chargeId, string $status, bool $isCaptured, ?float $refundedAmount): void
    {
        /** @var Payment $charge */
        $charge = Payment::where('charge_stripe_id', $chargeId)->first();
        if (!$charge) {
            throw new \Exception('Charge with id ' . $chargeId . 'does not exist.');
        }

        $refundedAmount = (float)$refundedAmount / 100;
        $charge->edit($status, $isCaptured, $refundedAmount);

        $charge->save();
    }
}
