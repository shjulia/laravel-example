<?php

declare(strict_types=1);

namespace App\Entities\Industry;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Rate - for overriding position
 *
 * @package App\Entities\Industry
 * @property int $id
 * @property string $title
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $position
 * @property-read Collection|Position[] $positions
 * @mixin \Eloquent
 */
class Rate extends Model
{
    /**
     * @var array
     */
    protected $guarded = [];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function positions()
    {
        return $this->belongsToMany(
            Position::class,
            'rate_position',
            'rate_id',
            'position_id'
        )
            ->withPivot(['rate', 'minimum_profit', 'surge_price', 'max_day_rate']);
    }

    public function getPositionAttribute()
    {
        return $this->positions[0]->pivot ?? null;
    }
}
