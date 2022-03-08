<?php

declare(strict_types=1);

namespace App\Entities\Shift;

use App\Entities\Data\Location\City;
use App\Entities\Industry\Position;
use App\Entities\Industry\Task;
use App\Entities\Notification\EmailMark;
use App\Entities\Payment\Charge;
use App\Entities\Payment\ProviderCharge;
use App\Entities\Review\Review;
use App\Entities\Statistics\MatchingSteps;
use App\Entities\User\Practice\AddressDTO;
use App\Entities\User\Practice\Practice;
use App\Entities\User\Practice\PracticeAddress;
use App\Entities\User\Provider\Specialist;
use App\Entities\User\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Shift - main Shift entity.
 * Practice looking for a provider to work in her clinic.
 *
 * @package App\Entities\Shift
 * @property int $id
 * @property int|null $parent_shift_id parent shift if shift is multi-day shift
 * @property int $practice_id practice which created shift
 * @property string $status
 * @property int $multi_days shift can be on several days, property tell how much days shift is
 * @property int|null $position_id provider position that practice is looking for
 * @property string|null $date start date
 * @property string|null $end_date
 * @property string|null $from_time
 * @property string|null $to_time
 * @property float|null $shift_time shift time in minutes
 * @property array|null $tasks requested tasks
 * @property float|null $cost money value that provider will get
 * @property float|null $cost_for_practice money that practice should pay for shift
 * @property float|null $arrival_time
 * @property int|null $provider_id matched provider id if exists
 * @property int|null $potential_provider_id last invited provider id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $answer
 * @property int $processed flag that tell that shift was processed and successfully completed
 * @property int $notified_finish flag that tell practice|provider reminded to finish shift
 * @property int|null $creator_id user with practice type id who created shift
 * @property int|null $coupon_id used by practice coupon id
 * @property int|null $location_id practice location id
 * @property float $surge_price bonus for provider calculated automatically
 * @property float $bonus bonus for provider
 * @property string|null $cancellation_charge_id payment system charge id for cancellation shift
 * @property float|null $cancellation_fee
 * @property string|null $cancellation_reason
 * @property string|null $reminder_status remind provider statuses about shift starts hours
 * @property string|null $invite_subject custom subject for invite email
 * @property int $is_floating flag if provider didn't show up and want to search for new provider
 * @property int $lunch_break lunch break in minutes
 * @property-read Collection|\App\Entities\Payment\Charge[] $charges all practice charges
 * @property-read Collection|\App\Entities\Shift\Shift[] $children children shifts for each day if multi-day shift
 * @property-read \App\Entities\Shift\Coupon|null $coupon
 * @property-read \App\Entities\User\User|null $creator
 * @property-read Collection|\App\Entities\Shift\Shift[] $freeChildren children shifts without provider
 * @property-read float|mixed|null $bonuses
 * @property-read bool $completed flag if shift is completed
 * @property-read float|mixed|null $cost_without_surge
 * @property-read bool $is_started_by_provider
 * @property-read AddressDTO $practice_location practice location (main or another)
 * @property-read Task[] $tasks_list
 * @property-read string $tasks_names
 * @property-read \App\Entities\User\Practice\PracticeAddress|null $location
 * @property-read Collection|\App\Entities\Shift\ShiftLog[] $logs logs with all shift actions
 * @property-read \App\Entities\Shift\Shift|null $parent
 * @property-read \App\Entities\Industry\Position|null $position
 * @property-read \App\Entities\User\Provider\Specialist|null $potentialProvider
 * @property-read \App\Entities\User\Practice\Practice $practice
 * @property-read \App\Entities\User\Provider\Specialist|null $provider
 * @property-read Collection|\App\Entities\Payment\ProviderCharge[] $providerCharges all charges for provider
 * @property-read Collection|\App\Entities\Review\Review[] $reviews
 * @property-read Collection|\App\Entities\Shift\ShiftInvite[] $shiftInvites
 * @property-read Collection|\App\Entities\Shift\ShiftTracking[] $shiftTracking
 * @property-read Collection|\App\Entities\Statistics\MatchingSteps[] $steps all matching steps
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Shift\Shift finished()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Shift\Shift real() not test shitfs
 * @mixin \Eloquent
 */
class Shift extends Model
{
    /**
     * @var array
     */
    protected $guarded = [];

    /**
     * @var array
     */
    protected $casts = ['tasks' => 'array'];

    public const STATUS_CREATING = "creating";
    public const STATUS_WAITING = "waiting";
    public const STATUS_MATCHING = "matching";
    public const STATUS_PARENT_MATCHING = "parent matching";
    public const STATUS_ACCEPTED_BY_PROVIDER = "accepted by provider";
    public const STATUS_CANCELED = "canceled";
    public const STATUS_CANCELED_BY_PRACTICE = "canceled by practice";
    public const STATUS_NO_PROVIDERS_FOUND = 'no providers found';
    public const STATUS_FINISHED = "finished";
    public const STATUS_ARCHIVED = "archived";
    public const REMINDER_STATUS_72 = 72;
    public const REMINDER_STATUS_24 = 24;
    public const REMINDER_STATUS_2 = 2;
    public const MIN_SHIFT_TIME = 120;
    public const HOUR = 60;
    public const MIN_TIME_BEFORE_SHIFT = 0.25;
    public const SHOW_UP_SURGE = 50;
    public const LUNCH_BREAK_30 = 30;
    public const LUNCH_BREAK_60 = 60;

    /**
     * @param Position $position
     * @param User $creator
     * @param Practice $practice
     * @return static
     */
    public static function createBase(Position $position, User $creator, Practice $practice): self
    {
        $shift = new self();
        $shift->position_id = $position->id;
        $shift->practice_id = $practice->id;
        $shift->creator_id = $creator->id;
        $shift->setCreatingStatus();
        return $shift;
    }

    /**
     * @param Position $position
     */
    public function editPosition(Position $position): void
    {
        $this->position_id = $position->id;
    }

    /**
     * @param string $startDate
     * @param string $endDate
     * @param string $fromTime
     * @param string $toTime
     * @param float $shiftTime
     * @param int $multiDays
     * @param int|null $lunchBreak
     */
    public function editDateTimeValues(
        string $startDate,
        string $endDate,
        string $fromTime,
        string $toTime,
        float $shiftTime,
        int $multiDays,
        ?int $lunchBreak = 0
    ): void {
        $this->date = $startDate;
        $this->end_date = $endDate;
        $this->from_time = $fromTime;
        $this->to_time = $toTime;
        $this->shift_time = $shiftTime;
        $this->lunch_break = $lunchBreak;
        $this->multi_days = $multiDays > 1 ? $multiDays : 0;
    }

    /**
     * @param float $costForProvider
     * @param float $costForPractice
     */
    public function editCosts(float $costForProvider, float $costForPractice): void
    {
        $this->cost = $costForProvider;
        $this->cost_for_practice = $costForPractice;
    }

    /**
     * @param int|null $providerId
     * @param float|null $arrivalTime
     */
    public function setPotentialProvider(?int $providerId = null, ?float $arrivalTime = null): void
    {
        $this->potential_provider_id = $providerId;
        $this->arrival_time = $arrivalTime;
    }

    /**
     * Recalculate shift times for floating shift
     */
    public function recalculateFloating(): void
    {
        if ($this->startsInHours() <= Shift::MIN_TIME_BEFORE_SHIFT) {
            if ($this->shift_time - Shift::HOUR < Shift::MIN_SHIFT_TIME) {
                throw new \DomainException('No providers found in time. Two hours before shift end.');
            }
            $newStartTime = Carbon::createFromTimeString($this->date . ' ' . $this->from_time)->addHour();
            $this->from_time = $newStartTime->format('H:i');
            $this->date = $newStartTime->format('Y-m-d');
            $this->shift_time = $this->shift_time - self::HOUR;
            if ($this->surge_price < self::SHOW_UP_SURGE) {
                $this->surge_price = self::SHOW_UP_SURGE;
            }
        }
        $this->provider_id = null;
        $this->setCreatingStatus();
        $this->is_floating = true;
    }

    /**
     * @param Specialist $provider
     */
    public function assignProviderToShift(Specialist $provider): void
    {
        if (!$this->multi_days) {
            $this->provider_id = $provider->user_id;
        }
        $this->setAcceptedByProviderStatus();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function provider()
    {
        return $this->belongsTo(Specialist::class, 'provider_id', 'user_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function potentialProvider()
    {
        return $this->belongsTo(Specialist::class, 'potential_provider_id', 'user_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function practice()
    {
        return $this->belongsTo(Practice::class, 'practice_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function steps()
    {
        return $this->hasMany(MatchingSteps::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function logs()
    {
        return $this->hasMany(ShiftLog::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function charges()
    {
        return $this->hasMany(Charge::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function providerCharges()
    {
        return $this->hasMany(ProviderCharge::class);
    }

    /**
     * @return Charge|null
     */
    public function lastCharge()
    {
        $charges = $this->charges()
            ->orderBy('id', 'DESC')
            ->where('is_main', true)
            ->get();
        $uncaptured = null;
        /** @var Charge $charge */
        foreach ($charges as $charge) {
            if ($charge->isCapturedReal()) {
                return $charge;
            }
            if ($charge->isUncapturedReal()) {
                $uncaptured = $charge;
            }
        }
        if ($uncaptured) {
            return $uncaptured;
        }
        return $charges[0] ?? null;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function shiftInvites()
    {
        return $this->hasMany(ShiftInvite::class);
    }

    /**
     * @return bool
     */
    public function isHasReviewFromProvider(): bool
    {
        return $this->reviews()->whereHas('practiceReview')->exists();
    }

    /**
     * @return bool
     */
    public function isHasReviewFromPractice(): bool
    {
        return $this->reviews()->whereHas('providerReview')->exists();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function reviewFromProvider()
    {
        return $this->reviews()
            ->whereHas('practiceReview')
            ->with('practiceReview.scores');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function reviewFromPractice()
    {
        return $this->reviews()
            ->whereHas('providerReview')
            ->with('providerReview.scores');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function position()
    {
        return $this->belongsTo(Position::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function emailMarks()
    {
        return $this->hasMany(EmailMark::class, 'entity_id', 'id');
    }

    /**
     * @return float
     */
    public function hours(): float
    {
        return round($this->shift_time / 60, 2);
    }

    /**
     * @return Task[]
     */
    public function getTasksListAttribute()
    {
        return Task::whereIn('id', $this->tasks)->get();
    }

    /**
     * @return bool
     */
    public function getCompletedAttribute()
    {
        return $this->isCompleted();
    }

    /**
     * @return void
     */
    public function setCreatingStatus(): void
    {
        $this->status = self::STATUS_CREATING;
    }

    /**
     * @return void
     */
    public function setAcceptedByProviderStatus(): void
    {
        $this->status = self::STATUS_ACCEPTED_BY_PROVIDER;
    }

    /**
     * @return void
     */
    public function setCanceledStatus(): void
    {
        $this->status = self::STATUS_CANCELED;
        $this->potential_provider_id = null;
    }

    /**
     * @return void
     */
    public function setCanceledByPracticeStatus(): void
    {
        $this->status = self::STATUS_CANCELED_BY_PRACTICE;
        $this->potential_provider_id = null;
    }

    /**
     * @return void
     */
    public function setFinishedStatus(): void
    {
        $this->status = self::STATUS_FINISHED;
    }

    /**
     * @return void
     */
    public function setMatchingStatus(): void
    {
        $this->status = self::STATUS_MATCHING;
    }

    /**
     * @return void
     */
    public function setParentMatchingStatus(): void
    {
        $this->status = self::STATUS_PARENT_MATCHING;
    }

    /**
     * @return void
     */
    public function setWaitingStatus(): void
    {
        $this->status = self::STATUS_WAITING;
    }

    /**
     * @return void
     */
    public function setNoProvidersFoundStatus(): void
    {
        $this->status = self::STATUS_NO_PROVIDERS_FOUND;
    }

    /**
     * @return bool
     */
    public function isAcceptedByProviderStatus(): bool
    {
        return $this->status === self::STATUS_ACCEPTED_BY_PROVIDER;
    }

    /**
     * @return bool
     */
    public function isFinishedStatus(): bool
    {
        return $this->status === self::STATUS_FINISHED;
    }

    /**
     * @return bool
     */
    public function isCompleted(): bool
    {
        if (!$this->multi_days) {
            return $this->isAcceptedByProviderStatus() && $this->processed;
        }
        $completed = true;
        /** @var self $child */
        foreach ($this->children as $child) {
            if (!$child->isCompleted()) {
                $completed = false;
                break;
            }
        }
        return $completed;
    }

    /**
     * @return bool
     */
    public function isMatchingStatus(): bool
    {
        return $this->status === self::STATUS_MATCHING;
    }

    /**
     * @return bool
     */
    public function isParentMatchingStatus(): bool
    {
        return $this->status === self::STATUS_PARENT_MATCHING;
    }

    /**
     * @return bool
     */
    public function isArchived(): bool
    {
        return $this->status === self::STATUS_ARCHIVED;
    }

    /**
     * @return bool
     */
    public function isCanceledStatus(): bool
    {
        return $this->status === self::STATUS_CANCELED
            || $this->status === self::STATUS_CANCELED_BY_PRACTICE;
    }

    /**
     * @return bool
     */
    public function isCanceledByPracticeStatus(): bool
    {
        return $this->status === self::STATUS_CANCELED_BY_PRACTICE;
    }

    /**
     * @return bool
     */
    public function isCreatingStatus(): bool
    {
        return $this->status === self::STATUS_CREATING;
    }

    /**
     * @return bool
     */
    public function isWaitingStatus(): bool
    {
        return $this->status === self::STATUS_WAITING;
    }

    public function setArchivedStatus(): void
    {
        $this->status = self::STATUS_ARCHIVED;
    }

    /**
     * @return bool
     */
    public function isNoPrividerFoundStatus(): bool
    {
        return $this->status === self::STATUS_NO_PROVIDERS_FOUND;
    }

    /**
     * @return string
     */
    public function statusName(): string
    {
        if ($this->isMatchingStatus() || $this->isParentMatchingStatus()) {
            return "Search in progress";
        }
        if ($this->isCanceledByPracticeStatus()) {
            return self::STATUS_CANCELED;
        }
        return $this->status;
    }

    /**
     * @return void
     */
    public function setNowDate(): void
    {
        $this->date = Carbon::today();
    }

    /**
     * @param string $chargeId
     * @param int $fee
     */
    public function setCancellationFee(string $chargeId, int $fee): void
    {
        $this->cancellation_charge_id = $chargeId;
        $this->cancellation_fee = $fee;
    }

    /**
     * @param string|null $reason
     */
    public function setCancellationReason(?string $reason): void
    {
        $this->cancellation_reason = $reason;
    }

    /**
     * @return float|mixed|null
     */
    public function getCostWithoutSurgeAttribute()
    {
        return $this->cost - $this->surge_price - $this->bonus;
    }

    /**
     * @return float|mixed|null
     */
    public function getBonusesAttribute()
    {
        return $this->surge_price + $this->bonus;
    }

    /**
     * @return bool
     */
    public function isProcessed(): bool
    {
        return (bool)$this->processed;
    }

    /**
     * @param Charge|null $charge
     * @return string
     */
    public function paymentStatusString(?Charge $charge = null): string
    {
        /** @var Charge $charge */
        $charge = $charge ?: $this->lastCharge();
        if (!$charge) {
            return 'not set';
        }
        return $charge->paymentStatusString();
    }

    /**
     * @return bool
     */
    public function canRefund(): bool
    {
        /** @var Charge $charge */
        $charge = $this->lastCharge();
        if (!$charge) {
            return false;
        }
        return $charge->isCapture() && !$charge->isRefund();
    }

    /**
     * @return bool
     */
    public function canBeCanceled(): bool
    {
        return !$this->isCanceledStatus() && !$this->isFinishedStatus() /*&& date('Y-m-d') <= $this->end_date*/ ;
    }

    /**
     * @return bool
     */
    public function canBeReMatched(): bool
    {
        return !$this->isMatchingStatus() &&
            !$this->isParentMatchingStatus() &&
            !$this->isArchived() &&
            ($this->startsInHours() > 0);
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFinished($query)
    {
        return $query->where(function ($q) {
            $q->where('status', self::STATUS_FINISHED)
                ->orWhere([
                    ['status', '=', self::STATUS_ACCEPTED_BY_PROVIDER],
                    ['processed', '=', 1]
                ]);
        });
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeReal($query)
    {
        return $query->whereHas('creator', function ($query) {
            $query->where('is_test_account', 0)
                ->active();
        });
    }

    /**
     * @return bool
     */
    public function isHasProvider(): bool
    {
        return (bool)$this->provider_id;
    }

    public function isHasProviderInChildren(): bool
    {
        if (!$this->multi_days) {
            return $this->isHasProvider();
        }
        /** @var self $child */
        foreach ($this->children as $child) {
            if ($child->isHasProvider()) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param Coupon $coupon
     */
    public function applyCoupon(Coupon $coupon): void
    {
        $costForPractice = $this->cost_for_practice;
        if ($coupon->percent_off) {
            $costForPractice = round($costForPractice * (1 - $coupon->percent_off / 100), 2);
        }
        if ($coupon->dollar_off) {
            $costForPractice = $costForPractice - $coupon->dollar_off;
        }
        $this->cost_for_practice = $costForPractice;
        $this->coupon_id = $coupon->id;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function location()
    {
        return $this->belongsTo(PracticeAddress::class, 'location_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function coupon()
    {
        return $this->belongsTo(Coupon::class, 'coupon_id', 'id');
    }

    /**
     * @return AddressDTO
     */
    public function getPracticeLocationAttribute()
    {
        $location = $this->location;
        if ($location) {
            return new AddressDTO(
                $location->practice_name,
                $location->address,
                $location->city,
                $location->state,
                $location->zip,
                $location->url,
                $location->lat,
                $location->lng,
                $location->practice_phone,
                $location->id
            );
        }
        $practice = $this->practice;
        return new AddressDTO(
            $practice->practice_name,
            $practice->address,
            $practice->city,
            $practice->state,
            $practice->zip,
            $practice->url,
            $practice->lat,
            $practice->lng,
            $practice->practice_phone
        );
    }

    /**
     * Formated shift time period
     * @param string|null $tz
     * @return string
     */
    public function period(?string $tz = null): string
    {
        if (!$this->date || !$this->from_time) {
            return 'not set';
        }
        $tz = $tz ?: $this->creator->tz;
        $date = Carbon::now($tz)->format('Y-m-d');
        $tomorrow = Carbon::now($tz)->addDays(1)->format('Y-m-d');
        if ($date == $this->date) {
            $start = 'Today';
        } elseif ($this->date == $tomorrow) {
            $start = 'Tomorrow';
        } else {
            $start = Carbon::createFromFormat('Y-m-d', $this->date, $tz)->format('F j, Y');
        }
        $fromTime = date('g:i A', strtotime($this->from_time));
        $toTime = date('g:i A', strtotime($this->to_time));
        return $start . ' ' . $fromTime . ' - '
            . ($this->date == $this->end_date ? '' : Carbon::createFromFormat(
                'Y-m-d',
                $this->end_date ?: $this->date,
                $tz
            )->format('F j, Y') . ' ')
            . $toTime;
    }

    /**
     * @param string|null $tz
     * @return string
     */
    public function datePeriod(?string $tz = null): string
    {
        if (!$this->date || !$this->from_time) {
            return 'not set';
        }
        $tz = $tz ?: $this->creator->tz;
        $date = Carbon::now($tz)->format('Y-m-d');
        $tomorrow = Carbon::now($tz)->addDays(1)->format('Y-m-d');
        if ($date == $this->date) {
            $start = 'Today';
        } elseif ($this->date == $tomorrow) {
            $start = 'Tomorrow';
        } else {
            $start = Carbon::createFromFormat('Y-m-d', $this->date, $tz)->format('F j, Y');
        }

        return $start
            . ($this->date == $this->end_date ? '' : ' - ' . Carbon::createFromFormat(
                'Y-m-d',
                $this->end_date ?: $this->date,
                $tz
            )->format('F j, Y') . ' ');
    }

    /**
     * Check if system need to send sms notification about shift
     * @return bool
     */
    public function isShouldSendText(): bool
    {
        if ($this->startsInHours() <= 2) {
            return true;
        }
        $now = Carbon::now($this->creator->tz);
        $period = [];
        $nowTime = $now->format('H:i');
        if ($now->isWeekday()) {
            $period[0] = "06:00";
            $period[1] = "22:30";
        } else {
            $period[0] = "09:00";
            $period[1] = "22:30";
        }

        return $nowTime > $period[0] && $nowTime < $period[1];
    }

    /**
     * @return Carbon|null
     */
    public function delayToSendText(): ?Carbon
    {
        if ($this->isShouldSendText()) {
            return null;
        }
        $tomorrow = Carbon::tomorrow($this->creator->tz);
        if ($tomorrow->isWeekday()) {
            return $tomorrow->setTime(6, 0);
        }
        return $tomorrow->setTime(9, 0);
    }

    /**
     * @return float
     */
    public function startsInHours(): float
    {
        if (!$this->from_time) {
            return 0;
        }
        $start = Carbon::createFromTimeString($this->date . ' ' . $this->from_time);
        $now = Carbon::now($this->creator->tz);
        $now = Carbon::createFromTimeString($now->format('Y-m-d H:i'));
        return round($now->diffInMinutes($start, false) / 60, 2);
    }

    /**
     * @return float
     */
    public function endsInHours(): float
    {
        if (!$this->to_time) {
            return 0;
        }
        $end = Carbon::createFromTimeString($this->end_date . ' ' . $this->to_time);
        $now = Carbon::now($this->creator->tz);
        $now = Carbon::createFromTimeString($now->format('Y-m-d H:i'));
        return round($now->diffInMinutes($end, false) / 60, 2);
    }

    /**
     * @return string
     */
    public function getTasksNamesAttribute(): string
    {
        $tasks = Task::whereIn('id', $this->tasks ?: [])->get()->pluck('title')->toArray();
        return implode(', ', $tasks);
    }

    /**
     * @return array
     */
    public static function getLunchTimes(): array
    {
        $lunchTimes = [self::LUNCH_BREAK_30, self::LUNCH_BREAK_60];
        return array_combine($lunchTimes, $lunchTimes);
    }

    /**
     * Create children shift for 1 day from parent shift
     * @param Shift $shift
     * @param string $date
     * @param bool|null $withoutReminder
     * @return static
     */
    public static function copyParentToChild(Shift $shift, string $date, ?bool $withoutReminder = false): self
    {
        $child = new self();
        $child->parent_shift_id = $shift->id;
        $child->practice_id = $shift->practice_id;
        $child->creator_id = $shift->creator_id;
        $child->setParentMatchingStatus();
        $child->position_id = $shift->position_id;
        $child->date = $date;
        $child->end_date = $date;
        $child->from_time = $shift->from_time;
        $child->to_time = $shift->to_time;
        $child->shift_time = $shift->shift_time / $shift->multi_days;
        $child->tasks = $shift->tasks;
        $child->cost = round($shift->cost / $shift->multi_days, 2);
        $child->cost_for_practice = round($shift->cost_for_practice / $shift->multi_days, 2);
        $child->surge_price = $shift->surge_price ? round($shift->surge_price / $shift->multi_days, 2) : 0;
        $child->bonus = $shift->bonus ? round($shift->bonus / $shift->multi_days, 2) : 0;
        $child->lunch_break = $shift->lunch_break;
        if (!$withoutReminder) {
            $child->reminder_status = self::REMINDER_STATUS_24;
        }
        if ($coupon = $shift->coupon) {
            $child->applyCoupon($coupon);
        }

        return $child;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function children()
    {
        return $this->hasMany(self::class, 'parent_shift_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function freeChildren()
    {
        return $this->children()
            ->where(function ($query) {
                $query->where('status', self::STATUS_PARENT_MATCHING)
                    ->orWhere('status', self::STATUS_MATCHING);
            });
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_shift_id', 'id');
    }

    /**
     * @param Shift $shift
     * @return bool
     */
    public function isChildOf(Shift $shift): bool
    {
        return $this->parent_shift_id === $shift->id;
    }

    /**
     * @return bool
     */
    public function isChild(): bool
    {
        return $this->parent_shift_id != null;
    }

    /**
     * @return bool
     */
    public function successChildrenExists(): bool
    {
        /** @var Shift $child */
        foreach ($this->children as $child) {
            if ($child->isFinishedStatus() || $child->isAcceptedByProviderStatus()) {
                return true;
            }
        }
        return false;
    }

    /**
     * @return float
     */
    public function freeCost(): float
    {
        return $this->freeChildren->sum('cost');
    }

    /**
     * @return string
     */
    public function providersName(): string
    {
        if (!$this->multi_days) {
            return $this->isHasProvider() ? $this->provider->user->full_name : 'empty';
        }
        $names = [];
        /** @var self $child */
        foreach ($this->children as $child) {
            if (!$child->isHasProvider()) {
                continue;
            }
            $names[] = $child->provider->user->full_name;
        }
        $names = array_unique($names);
        return !empty($names) ? implode(", ", $names) : 'empty';
    }

    /**
     * @param float $bonus
     */
    public function changeBonus(float $bonus): void
    {
        $this->cost = $this->cost - $this->bonus + $bonus;
        $this->bonus = $bonus;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function shiftTracking()
    {
        return $this->hasMany(ShiftTracking::class, 'shift_id', 'id');
    }

    /**
     * @return bool
     */
    public function getIsStartedByProviderAttribute()
    {
        return $this->shiftTracking()
            ->where('action', ShiftTracking::ACTION_STARTED)
            ->exists();
    }
}
