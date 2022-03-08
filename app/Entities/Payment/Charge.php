<?php

declare(strict_types=1);

namespace App\Entities\Payment;

use App\Entities\Shift\Shift;
use App\Entities\User\Practice\Practice;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Charge - Practice charge for shift
 *
 * @package App\Entities\Payment
 * @mixin \Eloquent
 * @property int $id
 * @property int $shift_id
 * @property int $practice_id
 * @property string|null $charge_stripe_id //now it's core charge id
 * @property float $amount
 * @property string $created
 * @property int $is_capture
 * @property int $is_refund
 * @property float|null $refund_amount
 * @property int $is_main flag tell if this charge is main or part charge
 * @property string|null $charge_status webhook  payment stratus
 * @property-read \App\Entities\User\Practice\Practice $practice
 * @property-read \App\Entities\Shift\Shift $shift
 */
class Charge extends Model
{
    /** @inheritdoc */
    protected $guarded = [];

    /** @inheritdoc */
    public $timestamps = false;

    /** @var string */
    public const CHARGE_STATUS_SENT = 'sent';

    /** @var string */
    public const CHARGE_STATUS_UNCAPTURED = 'uncaptured';

    /** @var string */
    public const CHARGE_STATUS_SUCCEEDED = 'succeeded';

    /** @var string */
    public const CHARGE_STATUS_CAPTURED = 'captured';

    /** @var string */
    public const CHARGE_STATUS_EXPIRED = 'expired';

    /** @var string */
    public const CHARGE_STATUS_FAILED = 'failed';

    /** @var string */
    public const CHARGE_STATUS_PENDING = 'pending';

    /** @var string */
    public const CHARGE_STATUS_REFUNDED = 'refunded';

    /** @var string */
    public const CHARGE_STATUS_UPDATED = 'updated';

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
    public function practice()
    {
        return $this->belongsTo(Practice::class, 'practice_id', 'id');
    }

    /**
     * Is charge frozen
     * @return bool
     */
    public function isCapture(): bool
    {
        return $this->is_capture == 0 ? true : false;
    }

    /**
     * is charge refunded
     * @return bool
     */
    public function isRefund(): bool
    {
        return (bool)$this->is_refund || ($this->charge_status == "refunded");
    }

    /**
     * @return bool
     */
    public function isPaid(): bool
    {
        return !$this->isCapture();
    }

    /**
     * @return string
     */
    public function paymentStatusString(): string
    {
        if ($this->charge_status) {
            return $this->charge_status;
        }
        return $this->isRefund() ? 'refunded'
            : ($this->isCapture() ? 'freezed'
                : ($this->isPaid() ? 'paid' : 'not set'));
    }

    public function setUncapturedStatus(): void
    {
        $this->charge_status = self::CHARGE_STATUS_UNCAPTURED;
    }

    public function setExpiredStatus(): void
    {
        $this->charge_status = self::CHARGE_STATUS_EXPIRED;
    }

    public function setRefundedStatus(): void
    {
        $this->charge_status = self::CHARGE_STATUS_REFUNDED;
    }

    public function setCapturedStatus(): void
    {
        $this->charge_status = self::CHARGE_STATUS_CAPTURED;
    }

    public function setStatus(string $status): void
    {
        $this->charge_status = $status;
    }

    public function increaseRefundAmount(int $amount): void
    {
        $this->refund_amount += $amount;
    }

    public function isCapturedReal(): bool
    {
        return $this->charge_status === self::CHARGE_STATUS_CAPTURED;
    }

    public function isUncapturedReal(): bool
    {
        return $this->charge_status === self::CHARGE_STATUS_UNCAPTURED;
    }

    public function edit(string $status, bool $isCaptured, ?float $refundedAmount): void
    {
        $this->charge_status = $status;
        $this->is_capture = $isCaptured;
        $this->refund_amount = $refundedAmount;
    }
}
