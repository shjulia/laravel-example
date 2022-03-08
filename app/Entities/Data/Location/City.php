<?php

declare(strict_types=1);

namespace App\Entities\Data\Location;

use App\Entities\Data\Tier;
use Illuminate\Database\Eloquent\Model;

/**
 * Class City - USA cities
 *
 * @package App\Entities\Data\Location
 * @mixin \Eloquent
 * @property int $id
 * @property string $name
 * @property int|null $tier
 * @property int|null $state
 * @property float $lat
 * @property float $lng
 * @property-read \App\Entities\Data\Location\Area $area
 */
class City extends Model
{
    /**
     * @inheritdoc
     */
    protected $guarded = [];

    /**
     * @inheritdoc
     */
    public $timestamps = false;

    public function getTier()
    {
        return $this->belongsTo(Tier::class, 'tier', 'id');
    }

    public function area()
    {
        return $this->belongsTo(Area::class);
    }
}
