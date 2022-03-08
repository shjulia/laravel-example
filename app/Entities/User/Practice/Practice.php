<?php

declare(strict_types=1);

namespace App\Entities\User\Practice;

use App\Entities\Data\Location\Area;
use App\Entities\Industry\Industry;
use App\Entities\Industry\Rate;
use App\Entities\Payment\Charge;
use App\Entities\Review\PracticeReview;
use App\Entities\Review\ProviderReview;
use App\Entities\Shift\Shift;
use App\Entities\User\User;
use App\Entities\User\Wallet\Wallet;
use App\Helpers\EncryptHelper;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

/**
 * Class Practice - Practice Entity.
 * Practice user type is for user who want to hire providers to job.
 *
 * @package App\Entities\User\Practice
 * @property int $id
 * @property int|null $industry_id
 * @property int|null $area_id
 * @property string|null $practice_name
 * @property string|null $address
 * @property string|null $city
 * @property string|null $state
 * @property string|null $zip
 * @property string|null $url
 * @property string|null $practice_phone
 * @property string|null $policy_photo
 * @property string|null $policy_type
 * @property string|null $policy_number
 * @property mixed|null $policy_expiration_date
 * @property string|null $policy_provider
 * @property int|null $no_policy
 * @property string|null $practice_photo
 * @property string|null $culture
 * @property string|null $notes
 * @property string|null $on_site_contact
 * @property string|null $park
 * @property string|null $door
 * @property string|null $dress_code
 * @property string|null $info
 * @property string|null $card_number
 * @property string|null $card_date
 * @property string|null $card_csv
 * @property string|null $stripe_client_id
 * @property int $hires_total total number of hires
 * @property float $average_stars average stars in reviews
 * @property int $reviews_to_provider_total total reviews given to providers
 * @property float $average_stars_to_provider average stars given to providers in reviews
 * @property float|null $lat practice home lat
 * @property float|null $lng practice home lng
 * @property float $paid_total total paid for providers
 * @property string|null $approval_status show if admin approved practice
 * @property int $reviews_total total reviews got
 * @property int|null $rate_id
 * @property int|null $tool_id
 * @property-read Collection|PracticeAddress[] $addresses all practice locations besides main location
 * @property-read \App\Entities\Data\Location\Area|null $area
 * @property-read Collection|\App\Entities\Shift\Shift[] $finishedShifts
 * @property-read string $create_date
 * @property-read string $full_address
 * @property-read string $policy_photo_url
 * @property-read string $practice_photo_url
 * @property-read \App\Entities\Industry\Industry|null $industry
 * @property-read \App\Entities\Industry\Rate|null $rate
 * @property-read Collection|\App\Entities\Review\PracticeReview[] $reviewsOwn
 * @property-read Collection|\App\Entities\Review\ProviderReview[] $reviewsToProvider
 * @property-read Collection|\App\Entities\Shift\Shift[] $shifts created shifts
 * @property-read Collection|\App\Entities\User\User[] $users users worked in practice (not providers)
 * @mixin \Eloquent
 */
class Practice extends Model
{
    /**
     * @var array
     */
    protected $guarded = [];

    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @var array
     */
    protected $casts = [
        'policy_expiration_date'  => 'date:Y-m-d',
    ];

    /** @var string */
    public const STATUS_APPROVED = "approved";
    /** @var string */
    public const STATUS_WAITING = "waiting";

    /**
     * @param Industry $industry
     * @return static
     */
    public static function createBase(Industry $industry): self
    {
        $practice = new self();
        $practice->industry_id = $industry->id;
        $practice->setWaitingStatus();
        return $practice;
    }

    /**
     * @param string $practiceName
     * @param string $address
     * @param string $city
     * @param string $state
     * @param string $zip
     * @param string|null $url
     * @param string|null $phone
     */
    public function setBaseInfo(
        string $practiceName,
        string $address,
        string $city,
        string $state,
        string $zip,
        ?string $url,
        ?string $phone
    ): void {
        $this->practice_name = $practiceName;
        $this->address = $address;
        $this->city = $city;
        $this->state = $state;
        $this->zip = $zip;
        $this->url = $url;
        $this->practice_phone = $phone;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'user_practice')
            ->withPivot('is_creator', 'practice_role');
    }

    /**
     * Return user who created practice
     * @return User|null
     */
    public function practiceCreator(): ?User
    {
        $users = $this->users;
        foreach ($this->users as $user) {
            if ($user->pivot->is_creator) {
                return $user;
            }
        }
        return $users[0] ?? null;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function industry()
    {
        return $this->belongsTo(Industry::class);
    }

    /**
     * @return string
     */
    public function getPolicyPhotoUrlAttribute(): string
    {
        return Storage::disk('s3')->url($this->policy_photo);
    }

    /**
     * @return string
     */
    public function getPracticePhotoUrlAttribute(): string
    {
        if (!$this->practice_photo) {
            return '';
        }
        return Storage::disk('s3')->url($this->practice_photo);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function reviewsOwn()
    {
        return $this->hasMany(PracticeReview::class, 'practice_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function reviewsToProvider()
    {
        return $this->hasMany(ProviderReview::class, 'from_practice_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function shifts()
    {
        return $this->hasMany(Shift::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function finishedShifts()
    {
        return $this->hasMany(Shift::class)->finished();
    }

    /**
     * @return string
     */
    public function getFullAddressAttribute(): string
    {
        return explode(",", $this->address)[0]
            . ', ' . $this->city
            . ', ' . $this->state . ' ' . $this->zip
            . ', ' . 'USA';
    }

    /**
     * @return bool
     */
    public function isSetPaymentInfo(): bool
    {
        $wallet = $this->practiceCreator()->wallet;
        return $wallet ? (bool)$wallet->has_payment_data : false;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function area()
    {
        return $this->belongsTo(Area::class);
    }

    /**
     * @return string
     */
    public function getCreateDateAttribute()
    {
        return $this->users[0]->created_at->format('Y-m-d');
    }

    /**
     * @return bool
     */
    public function isApproved(): bool
    {
        return $this->approval_status === self::STATUS_APPROVED;
    }

    public function setWaitingStatus(): void
    {
        $this->approval_status = self::STATUS_WAITING;
    }

    /**
     * @return bool
     */
    public function isWaiting(): bool
    {
        return $this->approval_status === self::STATUS_WAITING || !$this->approval_status;
    }

    public function changeApprovalStatus(): void
    {
        $this->approval_status = $this->isApproved() ? self::STATUS_WAITING : self::STATUS_APPROVED;
    }

    public static function getStatusList()
    {
        return [
            self::STATUS_WAITING => 'Waiting approval',
            self::STATUS_APPROVED => 'Active'
        ];
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function addresses()
    {
        return $this->hasMany(PracticeAddress::class, 'practice_id', 'id');
    }

    public function charges()
    {
        return $this->hasMany(Charge::class, 'practice_id', 'id');
    }

    /**
     * Check if practice have other locations
     * @return bool
     */
    public function isAddressesExists(): bool
    {
        return $this->addresses()->count() > 0;
    }

    /**
     * @deprecated
     * @return string
     */
    public function getEncryptedStripeCustomerId(): string
    {
        if (!$this->stripe_client_id) {
            return '';
        }
        return EncryptHelper::decrypt($this->stripe_client_id);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function rate()
    {
        return $this->belongsTo(Rate::class, 'rate_id', 'id');
    }


    /**
     * @param int $positionId
     * @return Rate|null
     */
    public function rateWithPos(int $positionId): ?Rate
    {
        $rate = $this->rate()
            ->with(['positions' => function ($q) use ($positionId) {
                $q->where('id', $positionId);
            }])
            ->whereHas('positions', function (Builder $q) use ($positionId) {
                $q->where('id', $positionId);
            })->first();
        if (!$rate) {
            return null;
        }
        $rate->setAppends(['position']);
        if (!isset($rate->position)) {
            return null;
        }
        return $rate;
    }

    /**
     * @param Rate|null $rate
     */
    public function setRate(?Rate $rate): void
    {
        $this->rate_id = $rate->id;
    }

    public function removeRate(): void
    {
        $this->rate_id = null;
    }

    public function getWallet(): ?Wallet
    {
        foreach ($this->users as $user) {
            if ($user->wallet && $user->wallet->has_payment_data) {
                return $user->wallet;
            }
        }
        return null;
    }
}

/**
 * @SWG\Definition(
 *     definition="Practice",
 *     type="object",
 *     @SWG\Property(property="id", type="integer"),
 *     @SWG\Property(property="industry_id", type="integer"),
 *     @SWG\Property(property="practice_name", type="string"),
 *     @SWG\Property(property="address", type="string"),
 *     @SWG\Property(property="city", type="string"),
 *     @SWG\Property(property="state", type="string"),
 *     @SWG\Property(property="zip", type="string"),
 *     @SWG\Property(property="url", type="string"),
 *     @SWG\Property(property="practice_phone", type="string"),
 *     @SWG\Property(property="policy_photo", type="string"),
 *     @SWG\Property(property="policy_type", type="string"),
 *     @SWG\Property(property="policy_number", type="string"),
 *     @SWG\Property(property="policy_expiration_date", type="string"),
 *     @SWG\Property(property="policy_provider", type="string"),
 *     @SWG\Property(property="no_policy", type="boolean"),
 *     @SWG\Property(property="practice_photo", type="string"),
 *     @SWG\Property(property="culture", type="string"),
 *     @SWG\Property(property="notes", type="string"),
 *     @SWG\Property(property="on_site_contact", type="string"),
 *     @SWG\Property(property="park", type="string"),
 *     @SWG\Property(property="door", type="string"),
 *     @SWG\Property(property="dress_code", type="string"),
 *     @SWG\Property(property="info", type="string"),
 *     @SWG\Property(property="stripe_client_id", type="string")
 * )
 */
