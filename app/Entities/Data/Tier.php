<?php

declare(strict_types=1);

namespace App\Entities\Data;

use App\Entities\Data\Location\Area;
use App\Entities\Data\Location\City;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Tier
 *
 * @package App\Entities\Data
 * @mixin \Eloquent
 * @property int $id
 * @property int $value
 * @property float $multiplier
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Entities\Data\Location\Area[] $areas
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Entities\Data\Location\City[] $cities
 */
class Tier extends Model
{
    /**
     * @var array
     */
    protected $guarded = [];

    /**
     * @var bool
     */
    public $timestamps = false;

    public function cities()
    {
        return $this->hasMany(City::class);
    }

    public function areas()
    {
        return $this->hasMany(Area::class);
    }
}
