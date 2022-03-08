<?php

declare(strict_types=1);

namespace App\Entities\Invite;

use App\Entities\Notification\EmailMark;
use App\Entities\User\Referral;
use App\Entities\User\User;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Invite - Invited User by referral program
 *
 * @package App\Entities\Invite
 * @property int $id
 * @property int $referral_id
 * @property string $email
 * @property int|null $user_id
 * @property int $accepted
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property float|null $bonus_value
 * @property string|null $status
 * @property string|null $payment_status
 * @property string|null $charge_id
 * @property string|null $payment_system
 * @property int $referral_notified
 * @property-read \App\Entities\User\Referral $referral
 * @property-read \App\Entities\User\User|null $user
 * @mixin \Eloquent
 */
class Invite extends Model
{
    /**
     * @var array
     */
    protected $guarded = [];

    /** @var int */
    public const NOT_ACCEPTED = 0;
    /** @var int */
    public const ACCEPTED = 1;
    /** @var string */
    public const STATUS_IN_BOON = 'in boon';
    /** @var string */
    public const STATUS_SENT = 'sent';
    /** @var string */
    public const STATUS_PAID = 'paid';

    public function setNotAcceptedStatus(): void
    {
        $this->accepted = self::NOT_ACCEPTED;
    }

    public function setAcceptedStatus(): void
    {
        $this->accepted = self::ACCEPTED;
    }

    public function isAccepted(): bool
    {
        return $this->accepted === self::ACCEPTED;
    }

    public function isNotAccepted(): bool
    {
        return $this->accepted === self::NOT_ACCEPTED;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function referral()
    {
        return $this->belongsTo(Referral::class, 'referral_id', 'user_id');
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
     * @param string $chargeId
     */
    public function setSentStatus(string $chargeId): void
    {
        $this->status = self::STATUS_SENT;
        $this->charge_id = $chargeId;
        $this->payment_system = 'dwolla';
    }

    public function setInSystemStatus(): void
    {
        $this->status = self::STATUS_IN_BOON;
    }

    /**
     * @param float $bonus
     */
    public function edit(float $bonus): void
    {
        $this->bonus_value = $bonus;
        $this->setInSystemStatus();
    }

    /**
     * @param string $paymentSystem
     * @param string|null $status
     * @param string|null $chargeId
     */
    public function editPayment(
        string $paymentSystem,
        string $status,
        ?string $chargeId = null
    ): void {
        $this->payment_system = $paymentSystem;
        $this->setStatus($status);
        $this->charge_id = $chargeId;
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
     * @return array
     */
    public static function statusesLists(): array
    {
        return [self::STATUS_IN_BOON, self::STATUS_SENT, self::STATUS_PAID];
    }
}
