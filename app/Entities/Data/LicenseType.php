<?php

declare(strict_types=1);

namespace App\Entities\Data;

use App\Entities\Industry\Position;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class LicenseType - Medical licenses to upload by provider in signup process
 *
 * @package App\Entities\Data
 * @property int $id
 * @property string $title
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Collection|\App\Entities\Data\LicenseTypePosition[] $licenseTypePositions
 * @property-read Collection|\App\Entities\Industry\Position[] $positions
 * @mixin \Eloquent
 */
class LicenseType extends Model
{
    /**
     * @var array
     */
    protected $guarded = [];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function licenseTypePositions()
    {
        return $this->hasMany(LicenseTypePosition::class, 'license_type_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function positions()
    {
        return $this->belongsToMany(
            Position::class,
            'license_types_positions',
            'license_type_id',
            'position_id'
        );
    }

    /**
     * @return array
     */
    public function getPositionsIdsAttribute()
    {
        return $this->positions->pluck('id')->toArray();
    }
}
