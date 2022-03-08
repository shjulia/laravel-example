<?php

namespace App\Fake\Services\Payment;

use App\Entities\Payment\Charge as Payment;
use App\Entities\Shift\Shift;
use App\Services\Payment\PaymentSetInterface;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Stripe\Error\Base;

class FakeStripeService implements PaymentSetInterface
{
    public const STRIPE_PRICE_FACTOR = 100;

    /**
     * @param string $token
     * @param string $email
     * @return string
     * @throws \Exception
     */
    public function createCustomer(string $token, string $email): string
    {
        return Str::uuid();
    }

    /**
     * @param string $stripeId
     * @return \Stripe\StripeObject
     */
    public function getCustomer(string $stripeId)
    {
    }

    /**
     * @param string $customerId
     * @param float $price
     * @param Shift $shift
     * @param bool|null $isFreeze
     * @param bool|null $isMain
     * @return mixed|void
     * @throws \Exception
     */
    public function createCharge(
        string $customerId,
        float $price,
        Shift $shift,
        ?bool $isFreeze = true,
        ?bool $isMain = true
    ) {
        $charge = Payment::create([
            'charge_stripe_id' => Str::uuid(),
            'shift_id' => $shift->id,
            'practice_id' => $shift->practice_id,
            'amount' => ($price / self::STRIPE_PRICE_FACTOR),
            'created' => Carbon::now()->toDateTimeString(),
            'is_capture' => false,
            'is_refund' => false
        ]);

        if (!$charge) {
            throw new \Exception(
                'Payment was successful, but there is problem with charge saving. Please contact the site admin'
            );
        }
    }

    public function captureCharge(Shift $shift)
    {
        $payment = Payment::where('shift_id', $shift->id)->first();
        if (!$payment) {
            return;
        }
        $payment->update(['is_capture' => true]);
    }

    /**
     * @param string $chargeId
     * @param float|null $sum
     * @return \Stripe\ApiResource|\Stripe\Refund|void
     * @throws \Exception
     */
    public function refund(string $chargeId, ?float $sum = null)
    {
        try {
            Payment::where('charge_stripe_id', $chargeId)->update(['is_refund' => 1]);
        } catch (Base $e) {
            throw new \Exception($e->getMessage());
        }
    }
}
