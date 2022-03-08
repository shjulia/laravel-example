<?php

declare(strict_types=1);

namespace App\Entities\Data;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Distance - distance and duration between practice office and provider home
 *
 * @package App\Entities\Data
 * @property int $id
 * @property int $practice_id
 * @property int $provider_id
 * @property float $distance
 * @property int|null $address_id
 * @property string $distance_text
 * @property float $duration
 * @property string $duration_text
 * @mixin \Eloquent
 */
class Distance extends Model
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
     * @return float
     */
    public function getDistanceInMiles(): float
    {
        return round($this->distance / 1000 * 0.621371, 2);
    }
}
