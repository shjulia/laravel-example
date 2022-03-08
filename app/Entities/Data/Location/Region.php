<?php

declare(strict_types=1);

namespace App\Entities\Data\Location;

use App\Entities\Data\State;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Region
 *
 * @package App\Entities\Data\Location
 * @mixin \Eloquent
 * @property int $id
 * @property string $name
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Entities\Data\State[] $states
 */
class Region extends Model
{
    /**
     * @inheritdoc
     */
    protected $guarded = [];

    /**
     * @inheritdoc
     */
    public $timestamps = false;

    public function states()
    {
        return $this->hasMany(State::class);
    }
}
