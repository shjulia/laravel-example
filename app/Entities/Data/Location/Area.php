<?php

declare(strict_types=1);

namespace App\Entities\Data\Location;

use App\Entities\Data\State;
use App\Entities\Data\Tier;
use App\Entities\User\Practice\Practice;
use App\Entities\User\Provider\Specialist;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Area - Custom area.
 * Can include zip codes and cities.
 *
 * @package App\Entities\Data\Location
 * @mixin \Eloquent
 * @property int $id
 * @property string $name
 * @property int|null $tier
 * @property int $is_open
 * @property int $state_id
 * @property-read Collection|\App\Entities\Data\Location\City[] $cities
 * @property-read array|null $geocode
 * @property-read Collection|\App\Entities\User\Practice\Practice[] $practices
 * @property-read Collection|\App\Entities\User\Provider\Specialist[] $specialists
 * @property-read \App\Entities\Data\State $state
 * @property-read Collection|\App\Entities\Data\Location\ZipCode[] $zipCodes
 */
class Area extends Model
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
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function cities()
    {
        return $this->belongsToMany(
            City::class,
            'area_places',
            'area_id',
            'city_id'
        );
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function zipCodes()
    {
        return $this->belongsToMany(
            ZipCode::class,
            'area_places',
            'area_id',
            'zip_id'
        );
    }

    /**
     * @return array|null
     */
    public function getGeocodeAttribute(): ?array
    {
        if (isset($this->cities[0])) {
            return ['lat' => $this->cities[0]->lat, 'lng' => $this->cities[0]->lng];
        }
        if (isset($this->zipCodes[0])) {
            return ['lat' => $this->zipCodes[0]->lat, 'lng' => $this->zipCodes[0]->lng];
        }
        return null;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function getTier()
    {
        return $this->belongsTo(Tier::class, 'tier', 'id');
    }

    /**
     * @return boolean
     */
    public function isOpen()
    {
        return $this->is_open;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function state()
    {
        return $this->belongsTo(State::class, 'state_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function specialists()
    {
        return $this->hasMany(Specialist::class, 'area_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function practices()
    {
        return $this->hasMany(Practice::class, 'area_id', 'id');
    }
}
