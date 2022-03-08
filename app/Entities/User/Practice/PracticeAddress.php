<?php

declare(strict_types=1);

namespace App\Entities\User\Practice;

use Illuminate\Database\Eloquent\Model;

/**
 * Class PracticeAddress - Practice locations besides main location
 *
 * @package App\Entities\User\Practice
 * @property int $id
 * @property int $practice_id
 * @property string|null $practice_name
 * @property string $address
 * @property string $city
 * @property string $state
 * @property string $zip
 * @property string|null $url
 * @property string|null $practice_phone
 * @property float|null $lat
 * @property float|null $lng
 * @property-read string $full_address
 * @property-read \App\Entities\User\Practice\Practice $practice
 * @mixin \Eloquent
 */
class PracticeAddress extends Model
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
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function practice()
    {
        return $this->belongsTo(Practice::class, 'practice_id', 'id');
    }

    /**
     * Get full practice address
     * @return string
     */
    public function getFullAddressAttribute(): string
    {
        return explode(", ", $this->address)[0]
            . ', ' . $this->city
            . ', ' . $this->state . ' ' . $this->zip
            . ', ' . 'USA';
    }
}
