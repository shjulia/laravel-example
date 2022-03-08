<?php

declare(strict_types=1);

namespace App\Entities\Data;

use App\Entities\Data\Location\Region;
use Illuminate\Database\Eloquent\Model;

/**
 * Class State - USA States
 *
 * @package App\Entities\Data
 * @property int $id
 * @property string $title
 * @property string $short_title
 * @property int|null $region_id
 * @property-read \App\Entities\Data\Location\Region $region
 * @mixin \Eloquent
 */
class State extends Model
{
    /**
     * @var array
     */
    protected $guarded = [];

    /**
     * @var bool
     */
    public $timestamps = false;


    public function region()
    {
        return $this->hasOne(Region::class);
    }
}
