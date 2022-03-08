<?php

declare(strict_types=1);

namespace App\Entities\User;

use App\Entities\User\Provider\Specialist;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Swagger\Annotations as SWG;

/**
 * Class License - Provider uploaded licenses
 *
 * @package App\Entities\User
 * @property int $id
 * @property int $specialist_id
 * @property int|null $parent_license_id
 * @property int $is_main
 * @property string|null $photo
 * @property int|null $type
 * @property string|null $number
 * @property mixed|null $expiration_date
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $position
 * @property string|null $state
 * @property string $approved_status
 * @property string|null $declined_reason
 * @property-read string $photo_url
 * @property-read \App\Entities\User\License $parent
 * @property-read \App\Entities\User\Provider\Specialist $specialist
 * @mixin \Eloquent
 */
class License extends Model
{
    /**
     * @var array
     */
    protected $guarded = [];

    /**
     * @var array
     */
    protected $appends = ['photo_url'];

    /**
     * @var array
     */
    protected $casts = [
        'expiration_date'  => 'date:Y-m-d',
    ];

    /** @var string  */
    public const STATUS_NOT_SET = "not set";

    /** @var string  */
    public const STATUS_APPROVED = "approved";

    /** @var string  */
    public const STATUS_DECLINED = "declined";

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function specialist()
    {
        return $this->belongsTo(Specialist::class, 'specialist_id', 'user_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function parent()
    {
        return $this->belongsTo(License::class, 'id', 'parent_license_id');
    }

    /**
     * @return string
     */
    public function getPhotoUrlAttribute(): string
    {
        if (!$this->photo) {
            return '';
        }
        return Storage::disk('s3')->url($this->photo);
    }

    /**
     * License approval method for SA
     */
    public function approve(): void
    {
        $this->approved_status = self::STATUS_APPROVED;
    }

    /**
     * License decline method for SA
     */
    public function decline(?string $reason = ''): void
    {
        $this->approved_status = self::STATUS_DECLINED;
        $this->declined_reason = $reason;
    }

    public function setBaseStatus(): void
    {
        $this->approved_status = self::STATUS_NOT_SET;
    }

    /**
     * @return bool
     */
    public function isApproved(): bool
    {
        return $this->approved_status === self::STATUS_APPROVED;
    }

    /**
     * @return bool
     */
    public function isDeclined(): bool
    {
        return $this->approved_status === self::STATUS_DECLINED;
    }

    /**
     * @return bool
     */
    public function isBaseStatus(): bool
    {
        return $this->approved_status === self::STATUS_NOT_SET;
    }

    /**
     * @return bool
     */
    public function isDateExpired(): bool
    {
        if (!$this->expiration_date) {
            return false;
        }
        return $this->expiration_date < date('Y-m-d');
    }
}

/**
 * @SWG\Definition(
 *     definition="License",
 *     type="object",
 *     @SWG\Property(property="id", type="integer"),
 *     @SWG\Property(property="specialist_id", type="integer"),
 *     @SWG\Property(property="photo", type="string"),
 *     @SWG\Property(property="type", type="string"),
 *     @SWG\Property(property="number", type="string"),
 *     @SWG\Property(property="expiration_date", type="string"),
 *     @SWG\Property(property="position", type="int"),
 *     @SWG\Property(property="state", type="string"),
 *     @SWG\Property(property="photo_url", type="string")
 * )
 */
