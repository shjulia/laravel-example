<?php

declare(strict_types=1);

namespace App\Entities\Payment;

use App\Entities\Shift\Shift;
use App\Entities\User\Provider\Specialist;
use Illuminate\Database\Eloquent\Model;

/**
 * @deprecated
 * Class ProviderCharge - Provider charges for shifts
 *
 * @package App\Entities\Payment
 * @property int $id
 * @property int $shift_id
 * @property int $provider_id
 * @property string|null $charge_id payment system charge id
 * @property string|null $payment_system
 * @property float $amount
 * @property string $status
 * @property string|null $payment_status
 * @property float|null $commission
 * @property int $is_main
 * @property float $decreased_amount
 * @property float $debt_covered_amount
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property float $expedited_commission_amount
 * @property-read mixed $charge_id_value
 * @property-read \App\Entities\User\Provider\Specialist $provider
 * @property-read \App\Entities\Shift\Shift $shift
 * @mixin \Eloquent
 */
class ProviderCharge extends Model
{
    /** @inheritdoc  */
    protected $guarded = [];

    /** @var string */
    public const STATUS_IN_BOON = 'in boon';
    /** @var string */
    public const STATUS_SENT = 'sent';
    /** @var string */
    public const STATUS_PAID = 'paid';
    /** @var string */
    public const STATUS_CANCELED = 'canceled';
    /** @var string */
    public const NO_PAYMENT_SYSTEM = 'no payment system';

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function shift()
    {
        return $this->belongsTo(Shift::class, 'shift_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function provider()
    {
        return $this->belongsTo(Specialist::class, 'provider_id', 'user_id');
    }

    /**
     * @return bool
     */
    public function isBaseStatus(): bool
    {
        return $this->status === self::STATUS_IN_BOON;
    }

    /**
     * @return bool
     */
    public function isSentStatus(): bool
    {
        return $this->status === self::STATUS_SENT;
    }

    /**
     * @return bool
     */
    public function isPaidStatus(): bool
    {
        return $this->status === self::STATUS_PAID;
    }

    /**
     * @return bool
     */
    public function isCanceledStatus(): bool
    {
        return $this->status === self::STATUS_CANCELED;
    }


    private function setBaseStatus(): void
    {
        $this->status = self::STATUS_IN_BOON;
    }

    /**
     * @param string|null $paymentSystem
     */
    public function setPaymentSystem(?string $paymentSystem = null): void
    {
        if (in_array($paymentSystem, self::paymentSystemLists())) {
            $this->payment_system = $paymentSystem;
            return;
        }
        $this->payment_system = self::NO_PAYMENT_SYSTEM;
    }

    /**
     * @param Shift $shift
     * @param float $amount
     * @param string|null $paymentSystem
     * @param bool|null $isMain
     * @return static
     */
    public static function createCharge(
        Shift $shift,
        float $amount,
        ?string $paymentSystem,
        ?bool $isMain = true
    ): self {
        $charge = new self();
        $charge->shift_id = $shift->id;
        $charge->provider_id = $shift->provider_id;
        $charge->amount = $amount;
        $charge->setBaseStatus();
        $charge->setPaymentSystem($paymentSystem);
        $charge->is_main = $isMain;
        return $charge;
    }

    /**
     * @param string $paymentSystem
     * @param float $commission
     * @param string|null $status
     * @param string|null $chargeId
     */
    public function editPayment(
        string $paymentSystem,
        ?float $commission,
        string $status,
        ?string $chargeId = null
    ): void {
        $this->setPaymentSystem($paymentSystem);
        $this->commission = $commission;
        $this->setStatus($status);
        $this->charge_id = $chargeId;
    }

    /**
     * @param float $amount
     */
    public function decreaseAmount(float $amount): void
    {
        if ($this->amount - $amount  <= 0) {
            throw new \DomainException('Main charge amount is less than decrease amount');
        }
        $this->amount = $this->amount - $amount;
        $this->decreased_amount = $this->decreased_amount + $amount;
    }

    /**
     * @return array
     */
    public static function paymentSystemLists(): array
    {
        return ['dwolla', 'check', 'venmo', 'cashApp'];
    }

    /**
     * @return bool
     */
    public function isCurrentPaymentStatus(): bool
    {
        return $this->payment_system === 'dwolla';
    }

    /**
     * @return array
     */
    public static function statusesLists(): array
    {
        return [self::STATUS_IN_BOON, self::STATUS_SENT, self::STATUS_PAID, self::STATUS_CANCELED];
    }

    /**
     * @param string|null $status
     */
    public function setStatus(?string $status = null): void
    {
        if (!$status) {
            $this->status = self::STATUS_IN_BOON;
            return;
        }
        if (!in_array($status, self::statusesLists())) {
            throw new \DomainException('Status is not valid');
        }
        $this->status = $status;
    }

    /**
     * @param string|null $transferId
     */
    public function setSentByCurrentSystem(?string $transferId = null): void
    {
        $this->setStatus(ProviderCharge::STATUS_SENT);
        $this->setPaymentSystem('dwolla');
        $this->charge_id = $transferId;
    }

    /**
     * @param float $debt
     */
    public function coverDebt(float $debt): void
    {
        $this->amount = $this->amount - $debt;
        $this->debt_covered_amount = $debt;
    }

    /**
     * @param float $amount
     * @param float $expeditedCommission
     */
    public function changeAmount(float $amount, float $expeditedCommission): void
    {
        $this->amount = $amount;
        $this->expedited_commission_amount = $expeditedCommission;
    }

    /**
     * @return string
     */
    public function getChargeIdValueAttribute(): string
    {
        if (!$this->charge_id) {
            return '';
        }
        $tmp = explode('/', $this->charge_id);
        return $tmp[count($tmp) - 1];
    }
}
