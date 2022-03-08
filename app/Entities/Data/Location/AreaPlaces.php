<?php

declare(strict_types=1);

namespace App\Entities\Data\Location;

use Illuminate\Database\Eloquent\Model;

/**
 * Class AreaPlaces - custom area place
 *
 * @package App\Entities\Data\Location
 * @mixin \Eloquent
 * @property int $id
 * @property int $area_id
 * @property int|null $city_id
 * @property int|null $zip_id
 * @property-read \App\Entities\Data\Location\Area $area
 */
class AreaPlaces extends Model
{
    /**
     * @inheritdoc
     */
    protected $guarded = [];

    /**
     * @inheritdoc
     */
    public $timestamps = false;

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function area()
    {
        return $this->belongsTo(Area::class);
    }
}
