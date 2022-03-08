<?php

declare(strict_types=1);

namespace App\Entities\Payment;

use App\Entities\User\Provider\Specialist;
use Illuminate\Database\Eloquent\Model;

/**
 * @deprecated
 * Class ProviderBonus - Provider Bonus data and charge
 *
 * @package App\Entities\Payment
 * @property int $id
 * @property int $provider_id
 * @property float $bonus_value
 * @property float|null $bonus_h hours worked value bonus
 * @property string $status
 * @property string|null $payment_status
 * @property string|null $charge_id
 * @property string|null $payment_system
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $desc bonus description
 * @property-read \App\Entities\User\Provider\Specialist $provider
 * @mixin \Eloquent
 */
class ProviderBonus extends Model
{
    /** @inheritdoc  */
    protected $guarded = [];

    /** @var string */
    public const STATUS_IN_BOON = 'in boon';
    /** @var string */
    public const STATUS_SENT = 'sent';
    /** @var string */
    public const STATUS_PAID = 'paid';

    /** @var float */
    public const FIRST_H = 40.0;
    /** @var float */
    public const ROAD_WARRIOR_BONUS_VAL = 10.0;

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
     * @param Specialist $provider
     * @param float $bonusValue
     * @param float|null $bonusH
     * @param string|null $desc
     * @return static
     */
    public static function createCharge(
        Specialist $provider,
        float $bonusValue,
        ?float $bonusH,
        ?string $desc = null
    ): self {
        $charge = new self();
        $charge->provider_id = $provider->user_id;
        $charge->bonus_value = $bonusValue;
        $charge->bonus_h = $bonusH;
        $charge->desc = $desc;
        return $charge;
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
}
