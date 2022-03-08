<?php

declare(strict_types=1);

namespace App\Entities\User\Provider;

use App\Entities\Data\Holiday;
use App\Entities\Data\Location\Area;
use App\Entities\Industry\Industry;
use App\Entities\Industry\Position;
use App\Entities\Industry\Speciality;
use App\Entities\Industry\Task;
use App\Entities\Payment\ProviderBonus;
use App\Entities\Payment\ProviderCharge;
use App\Entities\Review\PracticeReview;
use App\Entities\Review\ProviderReview;
use App\Entities\Review\Review;
use App\Entities\Shift\Shift;
use App\Entities\Shift\ShiftInvite;
use App\Entities\User\License;
use App\Entities\User\User;
use App\Helpers\EncryptHelper;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Class Specialist - User with type provider.
 * Provider is a person who is looking for a job
 *
 * @package App\Entities\User
 * @property int $user_id
 * @property int|null $industry_id
 * @property int|null $position_id
 * @property int|null $area_id
 * @property string|null $driver_photo
 * @property string|null $driver_address
 * @property string|null $driver_city
 * @property string|null $driver_state
 * @property string|null $driver_zip
 * @property string|null $driver_first_name
 * @property string|null $driver_last_name
 * @property string|null $driver_middle_name
 * @property mixed|null $driver_expiration_date
 * @property string|null $driver_gender
 * @property string|null $dob
 * @property string|null $ssn
 * @property string|null $driver_license_number
 * @property string|null $photo
 * @property float|null $photos_similar
 * @property string|null $driver_croped_photo
 * @property int $jobs_total total jobs worked
 * @property float $hours_total total hours worked
 * @property int $reviews_total total reviews got
 * @property float $average_stars average stars in reviews
 * @property int $reviews_to_practice_total total amount giving reviews to practice
 * @property float $average_stars_to_practice average stars giving to practice
 * @property float|null $lat home lat
 * @property float|null $lng home lng
 * @property int $available (0 or 1) show if provider is available to work
 * @property float $paid_total show how much money provider got in boon for all time
 * @property string|null $approval_status show if admin approved provider
 * @property int|null $shift_length_min
 * @property int|null $shift_length_max
 * @property int|null $shift_distance_max
 * @property int|null $shift_duration_max
 * @property string|null $payment_regime_status same-day pay with commission or expedited payment without commission
 * @property float $debt debt to system if shift was overpaid
 * @property float|null $last_lat last lat in the way to shift
 * @property float|null $last_lng last lng tin the way to shift
 * @property float|null $min_rate
 * @property string|null $approval_reason approval reason if provider shouldn't be approved
 * @property int|null $tool_id
 * @property-read \App\Entities\Data\Location\Area|null $area
 * @property-read Collection|ProviderAvailability[] $availabilities
 * @property-read \App\Entities\User\Provider\Checkr $checkr
 * @property-read \stdClass $additional
 * @property-read mixed $create_date
 * @property-read string $driver_photo_url
 * @property-read string $full_address
 * @property-read mixed $full_name
 * @property-read mixed $id
 * @property-read string $photo_url
 * @property-read string|null $ssn_val decoded ssn value
 * @property-read Collection|\App\Entities\Data\Holiday[] $holidays availabilities at holidays
 * @property-read \App\Entities\Industry\Industry|null $industry
 * @property-read Collection|\App\Entities\User\License[] $licenses
 * @property-read \App\Entities\User\Provider\ProviderMoney $money
 * @property-read \App\Entities\Industry\Position|null $position
 * @property-read Collection|\App\Entities\Payment\ProviderBonus[] $providerBonuses
 * @property-read Collection|\App\Entities\Review\ProviderReview[] $reviewsOwn
 * @property-read Collection|\App\Entities\Review\PracticeReview[] $reviewsToPractice
 * @property-read Collection|\App\Entities\Industry\Task[] $routineTasks
 * @property-read Collection|\App\Entities\Shift\ShiftInvite[] $shiftInvites
 * @property-read Collection|\App\Entities\Shift\Shift[] $shifts
 * @property-read Collection|\App\Entities\Industry\Speciality[] $specialities
 * @property-read \App\Entities\User\User $user
 * @mixin \Eloquent
 */
class Specialist extends Model
{
    /**
     * Has relation one to one with User
     *
     * @var string
     */
    protected $primaryKey = "user_id";

    /**
     * @var array
     */
    protected $guarded = [];

    /** @var string */
    public const STATUS_APPROVED = "approved";
    /** @var string */
    public const STATUS_WAITING = "waiting";
    /** @var string */
    public const STANDARD_PAYMENT_STATUS = "standard";
    /** @var string */
    public const EXPEDITED_PAYMENT_STATUS = "expedited";
    /** @var string */
    public const DUPLICATE_STATUS = 'duplicate';
    /** @var int */
    public const DISTANCE_MAX_DEFAULT = 25;

    /**
     * @var array
     */
    protected $casts = [
        'driver_expiration_date' => 'date:Y-m-d',
    ];

    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @param User $user
     * @param Industry $industry
     * @return static
     */
    public static function createBase(User $user, Industry $industry): self
    {
        $provider = new self();
        $provider->user_id = $user->id;
        $provider->industry_id = $industry->id;
        $provider->available = 0;
        $provider->shift_distance_max = self::DISTANCE_MAX_DEFAULT;
        $provider->setWaitingStatus();
        return $provider;
    }

    /**
     * @return bool
     */
    public function isSetTransferInfo(): bool
    {
        $wallet = $this->user->wallet;
        return $wallet ? (bool)$wallet->has_transfer_data : false;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function licenses()
    {
        return $this->hasMany(License::class, 'specialist_id', 'user_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function industry()
    {
        return $this->belongsTo(Industry::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function position()
    {
        return $this->belongsTo(Position::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function checkr()
    {
        return $this->hasOne(Checkr::class, 'specialist_id');
    }

    /**
     * @deprecated
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function money()
    {
        return $this->hasOne(ProviderMoney::class, 'provider_id', 'user_id');
    }

    /**
     * @return string
     */
    public function getDriverPhotoUrlAttribute()
    {
        return Storage::disk('s3')->url($this->driver_photo);
    }

    /**
     * @return string
     */
    public function getPhotoUrlAttribute()
    {
        return $this->photo ? Storage::disk('s3')->url($this->photo) : '';
    }

    public function getCreateDateAttribute()
    {
        return $this->user->created_at->format('Y-m-d');
    }

    public function getIdAttribute()
    {
        return $this->user_id;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function specialities()
    {
        return $this->belongsToMany(
            Speciality::class,
            'specialist_speciality',
            'specialist_id',
            'speciality_id'
        );
    }

    public function routineTasks()
    {
        return $this->belongsToMany(
            Task::class,
            'specialist_task',
            'user_id',
            'task_id'
        );
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function holidays()
    {
        return $this->belongsToMany(
            Holiday::class,
            'provider_holidays_availabilities',
            'specialist_id',
            'holiday_id'
        );
    }

    /**
     * Time availabilities (day, time from, time to)
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function availabilities()
    {
        return $this->hasMany(ProviderAvailability::class, 'specialist_id', 'user_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function providerBonuses()
    {
        return $this->hasMany(ProviderBonus::class, 'provider_id', 'user_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function providerCharges()
    {
        return $this->hasMany(ProviderCharge::class, 'provider_id', 'user_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function reviewsOwn()
    {
        return $this->hasMany(ProviderReview::class, 'provider_id', 'user_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function reviewsToPractice()
    {
        return $this->hasMany(PracticeReview::class, 'from_provider_id', 'user_id');
    }

    /**
     * Shifts where provider was matched
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function shifts()
    {
        return $this->hasMany(Shift::class, 'provider_id', 'user_id');
    }

    /**
     * All invites to job for provider
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function shiftInvites()
    {
        return $this->hasMany(ShiftInvite::class, 'provider_id', 'user_id');
    }

    /**
     * @return string
     */
    public function getFullAddressAttribute(): string
    {
        return explode(",", $this->driver_address)[0]
            . ', ' . $this->driver_city
            . ', ' . $this->driver_state . ' ' . $this->driver_zip
            . ', ' . 'USA';
    }

    public function getFullNameAttribute(): string
    {
        if (!$this->driver_first_name || !$this->driver_last_name) {
            return '';
        }
        return $this->driver_first_name . ' ' . $this->driver_middle_name . ' ' . $this->driver_last_name;
    }

    /**
     * All parsed availabilities
     * @return \stdClass
     */
    public function getAdditionalAttribute(): \stdClass
    {
        $data = new \stdClass();
        $specialities = [];
        foreach ($this->specialities as $speciality) {
            $specialities[] = $speciality->id;
        }
        $data->specialities = $specialities;
        //$data->availabilities = $this->availabilities->keyBy('day');
        $avl = [];
        $availabilities = $this->availabilities->groupBy(function ($item, $key) {
                return $item["from_hour"] . "-" . $item["to_hour"];
        })->toArray();
        foreach ($availabilities ?: [] as $value) {
            $days = [];
            foreach ($value as $av) {
                $days[] = $av['day'];
            }
            $avl[] = ['from' => $av['from_hour'], 'to' => $av['to_hour'], 'inDays' => $days, 'id' => $av['id']];
        }
        $data->availabilities = $avl;
        $data->holidays = $this->holidays->keyBy('id');

        return $data;
    }

    /**
     * @param array|null $days
     * @param array|null $from
     * @param array|null $to
     * @return array
     */
    public function parsedAvailabilities(?array $days, ?array $from, ?array $to): array
    {
        $avl = [];
        if (!$days || !$from || !$to) {
            return [];
        }
        foreach ($days as $key => $val) {
            if (!$val || !$from[$key] || !$to[$key]) {
                continue;
            }
            $avl[] = [
                'from' => $from[$key],
                'to' => $to[$key],
                'inDays' => explode(',', $val),
                'id' => (string)Str::uuid()
            ];
        }
        return $avl;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function area()
    {
        return $this->belongsTo(Area::class);
    }

    /**
     * @return string|null
     */
    public function getSsnValAttribute(): ?string
    {
        if (!$this->ssn) {
            return null;
        }
        return EncryptHelper::decrypt($this->ssn);
    }

    /**
     * @return bool
     */
    public function isEnableForCheckr(): bool
    {
        if ($this->dob && $this->ssn && $this->driver_zip && $this->driver_license_number && $this->driver_state) {
            return true;
        }
        return false;
    }

    /**
     * @return bool
     */
    public function isApproved(): bool
    {
        return $this->approval_status === self::STATUS_APPROVED;
    }

    /**
     * @return bool
     */
    public function isWaiting(): bool
    {
        return $this->approval_status === self::STATUS_WAITING;
    }

    public function setWaitingStatus(): void
    {
        $this->approval_status = self::STATUS_WAITING;
    }

    /**
     * @param string|null $reason
     */
    public function changeApprovalStatus(?string $reason = null): void
    {
        $this->approval_status = $this->isApproved() ? self::STATUS_WAITING : self::STATUS_APPROVED;
        $this->approval_reason = $reason;
    }

    public function setStandardPaymentStatus(): void
    {
        $this->payment_regime_status = self::STANDARD_PAYMENT_STATUS;
    }

    public function setExpeditedPaymentStatus(): void
    {
        $this->payment_regime_status = self::EXPEDITED_PAYMENT_STATUS;
    }

    /**
     * @return bool
     */
    public function isStandardPaymentStatus(): bool
    {
        return $this->payment_regime_status === self::STANDARD_PAYMENT_STATUS ||
            $this->payment_regime_status === null;
    }

    public function isDuplicate(): bool
    {
        return $this->approval_status === self::DUPLICATE_STATUS;
    }

    public function setDuplicateStatus(): void
    {
        $this->approval_status = self::DUPLICATE_STATUS;
    }

    public static function getStatusList()
    {
        return [
            self::STATUS_WAITING => 'Waiting approval',
            self::STATUS_APPROVED => 'Active',
            self::DUPLICATE_STATUS => 'Duplicate'
        ];
    }

    /**
     * @return bool
     */
    public function isExpeditedPaymentStatus(): bool
    {
        return $this->payment_regime_status === self::EXPEDITED_PAYMENT_STATUS;
    }

    /**
     * @param float $debt
     */
    public function increaseDebt(float $debt): void
    {
        $this->debt = $this->debt + $debt;
    }

    public function debtToZero(): void
    {
        $this->debt = 0;
    }

    /**
     * @param float $lat
     * @param float $lng
     */
    public function markLastLocation(float $lat, float $lng): void
    {
        $this->last_lat = $lat;
        $this->last_lng = $lng;
    }

    /**
     * @param float $minRate
     */
    public function setMinRate(float $minRate): void
    {
        $this->min_rate = $minRate;
    }

    /**
     * @param float $rate
     * @return bool
     */
    public function isRateSuite(float $rate): bool
    {
        if (!$this->min_rate) {
            return true;
        }
        return $this->min_rate <= $rate;
    }

    /**
     * @return bool
     */
    public function canBeApproved(): bool
    {
        $user = $this->user;
        if (!$user->isSignupFinished()) {
            return false;
        }
        if (!$this->checkr || !$this->checkr->isClear()) {
            return false;
        }
        if (!$this->photos_similar || $this->photos_similar < 85) {
            return false;
        }
        $res = true;
        foreach ($this->licenses as $license) {
            if (!$license->isApproved()) {
                $res = false;
            }
        }
        return $res;
    }

    public function setAvailable()
    {
        $this->available = true;
    }

    public function setUnavailable()
    {
        $this->available = false;
    }

    public function hasPhoto(): bool
    {
        return (bool)$this->photo;
    }
}

/**
 * @SWG\Definition(
 *     definition="Provider",
 *     type="object",
 *     @SWG\Property(property="user_id", type="integer"),
 *     @SWG\Property(property="industry_id", type="integer"),
 *     @SWG\Property(property="position_id", type="integer"),
 *     @SWG\Property(property="driver_photo", type="string"),
 *     @SWG\Property(property="driver_address", type="string"),
 *     @SWG\Property(property="driver_city", type="string"),
 *     @SWG\Property(property="driver_state", type="string"),
 *     @SWG\Property(property="driver_zip", type="string"),
 *     @SWG\Property(property="driver_first_name", type="string"),
 *     @SWG\Property(property="driver_last_name", type="string"),
 *     @SWG\Property(property="driver_middle_name", type="string"),
 *     @SWG\Property(property="driver_expiration_date", type="string"),
 *     @SWG\Property(property="driver_gender", type="string"),
 *     @SWG\Property(property="dob", type="string"),
 *     @SWG\Property(property="ssn", type="string"),
 *     @SWG\Property(property="driver_license_number", type="string"),
 *     @SWG\Property(property="photo", type="string"),
 *     @SWG\Property(property="checkr_status", type="string"),
 *     @SWG\Property(property="checkr_candidate_id", type="string"),
 *     @SWG\Property(property="checkr_report_id", type="string"),
 *     @SWG\Property(property="checkr_error_response", type="string"),
 *     @SWG\Property(property="checkr_success_response", type="string"),
 *     @SWG\Property(property="checkr_attempts", type="integer"),
 *     @SWG\Property(property="photos_similar", type="number"),
 *     @SWG\Property(property="driver_croped_photo", type="string"),
 *     @SWG\Property(property="jobs_total", type="integer"),
 *     @SWG\Property(property="hours_total", type="number"),
 *     @SWG\Property(property="reviews_total", type="integer"),
 *     @SWG\Property(property="average_stars", type="number"),
 *     @SWG\Property(property="reviews_to_practice_total", type="integer"),
 *     @SWG\Property(property="average_stars_to_practice", type="number"),
 *     @SWG\Property(property="lat", type="number"),
 *     @SWG\Property(property="lng", type="number"),
 *     @SWG\Property(property="licenses", type="array", @SWG\Items(ref="#/definitions/License")),
 * )
 */
