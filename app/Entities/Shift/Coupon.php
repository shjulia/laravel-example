<?php

declare(strict_types=1);

namespace App\Entities\Shift;

use App\Entities\Data\State;
use App\Entities\Industry\Position;
use App\Entities\User\Practice\Practice;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Coupon - Coupon Entity to decrease shift cost for practice.
 *
 * @package App\Entities\Shift
 * @property int $id
 * @property string $code
 * @property string $start_date
 * @property string $end_date
 * @property float|null $dollar_off
 * @property float|null $percent_off
 * @property float|null $minimum_bill
 * @property int|null $use_per_account_limit amount times one practice can use coupon
 * @property int|null $use_globally_limit amount times coupon can be used
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $practice_id
 * @mixin \Eloquent
 */
class Coupon extends Model
{
    /**
     * @var array
     */
    protected $guarded = [];

    /**
     * @param string $code
     * @param \DateTimeImmutable $start
     * @param \DateTimeImmutable $end
     * @param float|null $percent
     * @param float|null $dollarsOff
     * @param int|null $accLimit
     * @param int|null $globalLimit
     * @param float|null $minimumBill
     * @param Practice|null $practice
     * @return static
     */
    public static function createBase(
        string $code,
        \DateTimeImmutable $start,
        \DateTimeImmutable $end,
        ?float $percent,
        ?float $dollarsOff,
        ?int $accLimit = null,
        ?int $globalLimit = null,
        ?float $minimumBill = null,
        ?Practice $practice = null
    ): self {
        $coupon = new self();
        $coupon->code = $code;
        $coupon->start_date = $start->format('Y-m-d');
        $coupon->end_date = $end->format('Y-m-d');
        $coupon->percent_off = $percent;
        $coupon->dollar_off = $dollarsOff;
        $coupon->use_per_account_limit = $accLimit;
        $coupon->use_globally_limit = $globalLimit;
        $coupon->minimum_bill = $minimumBill;
        $coupon->practice_id = $practice ? $practice->id : null;
        return $coupon;
    }

    /**
     * @param \DateTimeImmutable $start
     * @param \DateTimeImmutable $end
     */
    public function editTime(
        \DateTimeImmutable $start,
        \DateTimeImmutable $end
    ): void {
        $this->start_date = $start->format('Y-m-d');
        $this->end_date = $end->format('Y-m-d');
    }

    /**
     * @param float|null $percent
     * @param float|null $dollarsOff
     * @param int $accLimit
     * @param int $globalLimit
     * @param float|null $minimumBill
     */
    public function edit(
        ?float $percent,
        ?float $dollarsOff,
        int $accLimit,
        int $globalLimit,
        ?float $minimumBill
    ): void {
        $this->percent_off = $percent;
        $this->dollar_off = $dollarsOff;
        $this->use_per_account_limit = $accLimit;
        $this->use_globally_limit = $globalLimit;
        $this->minimum_bill = $minimumBill;
    }

    /**
     * valid states for coupon usages
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function states()
    {
        return $this->belongsToMany(State::class, 'coupon_state', 'coupon_id', 'state_id');
    }

    /**
     * valid positions for coupon usages
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function positions()
    {
        return $this->belongsToMany(Position::class, 'coupon_position', 'coupon_id', 'position_id');
    }

    /**
     * shift where coupon was used
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function shifts()
    {
        return $this->hasMany(Shift::class, 'coupon_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function practice()
    {
        return $this->hasOne(Practice::class, 'id', 'practice_id');
    }

    /**
     * @param float $cost
     * @throws \DomainException
     */
    public function checkValidByBill(float $cost): void
    {
        if ($this->minimum_bill && $cost < $this->minimum_bill) {
            throw new \DomainException('Minimum bill for this coupon should be $' . $this->minimum_bill);
        }
    }

    /**
     * @param int $positionId
     * @throws \DomainException
     */
    public function checkValidByPosition(int $positionId): void
    {
        if ($this->positions->isEmpty()) {
            return;
        }
        $has = false;
        foreach ($this->positions as $position) {
            if ($position->id == $positionId) {
                $has = true;
                break;
            }
        }
        if (!$has) {
            throw new \DomainException('This Coupon code is not valid for selected position.');
        }
    }

    /**
     * @param string $stateTitle
     */
    public function checkValidByState(string $stateTitle): void
    {
        if ($this->states->isEmpty()) {
            return;
        }
        $has = false;
        foreach ($this->states as $state) {
            if ($state->short_title == $stateTitle) {
                $has = true;
                break;
            }
        }
        if (!$has) {
            throw new \DomainException('This Coupon code is not valid for your state.');
        }
    }

    /**
     * @param int $count
     * @throws \DomainException
     */
    public function checkValidByGloballyLimit(int $count): void
    {
        if ($count >= $this->use_globally_limit) {
            throw new \DomainException('Coupon code have been expired by usages.');
        }
    }

    /**
     * @param int $count
     * @throws \DomainException
     */
    public function checkValidByAccountLimit(int $count): void
    {
        if ($count >= $this->use_per_account_limit) {
            throw new \DomainException('Coupon code have been expired by usages.');
        }
    }

    /**
     * @param string|null $tz
     */
    public function checkValidByTime(?string $tz = null): void
    {
        $date = Carbon::now($tz)->format('Y-m-d');
        if ($date < $this->start_date) {
            throw new \DomainException('Coupon will be valid from' . $this->start_date);
        }
        if ($date > $this->end_date) {
            throw new \DomainException('Coupon has expired');
        }
    }

    /**
     * @return bool
     */
    public function isExpired(): bool
    {
        return $this->end_date < now()->format('Y-m-d');
    }
}
