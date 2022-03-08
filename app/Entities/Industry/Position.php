<?php

declare(strict_types=1);

namespace App\Entities\Industry;

use App\Entities\Data\LicenseType;
use App\Entities\User\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Position - for practices and providers union
 *
 * @package App\Entities\Industry
 * @property int $id
 * @property string $title
 * @property int $industry_id
 * @property float $fee pay fo 1 hour
 * @property float $minimum_profit minimum profit for system for short shifts
 * @property int|null $parent_id
 * @property float $surge_price surge price value for position request
 * @property-read Collection|\App\Entities\Industry\Position[] $children
 * @property-read \App\Entities\Industry\Industry $industry
 * @property-read Collection|\App\Entities\Data\LicenseType[] $licenseTypes
 * @property-read \App\Entities\Industry\Position|null $parent
 * @property-read Collection|\App\Entities\User\User[] $providerUsers
 * @mixin \Eloquent
 */
class Position extends Model
{
    /**
     * @var array
     */
    protected $guarded = [];

    /**
     * @var bool
     */
    public $timestamps = false;

    /** @var float */
    public const COMMISSION_VALUE = 1.2;

    /**
     * @param string $title
     * @param Industry $industry
     * @param float $fee
     * @param float $minProfit
     * @param float $surgePrice
     * @param int|null $parentId
     * @return Position
     */
    public static function createNew(
        string $title,
        Industry $industry,
        float $fee,
        float $minProfit,
        float $surgePrice,
        ?int $parentId = null
    ): self {
        $position = new self();
        $position->title = $title;
        $position->industry_id = $industry->id;
        $position->fee = $fee;
        $position->minimum_profit = $minProfit;
        $position->surge_price = $surgePrice;
        $position->parent_id = $parentId;
        return $position;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function industry()
    {
        return $this->belongsTo(Industry::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function children()
    {
        return$this->hasMany(self::class, 'parent_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function parent()
    {
        return$this->belongsTo(self::class, 'parent_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function providerUsers()
    {
        return $this->belongsToMany(
            User::class,
            'specialists',
            'position_id',
            'user_id'
        );
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function licenseTypes()
    {
        return $this->belongsToMany(
            LicenseType::class,
            'license_types_positions',
            'position_id',
            'license_type_id'
        );
    }
}
