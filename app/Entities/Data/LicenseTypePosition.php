<?php

declare(strict_types=1);

namespace App\Entities\Data;

use App\Entities\Industry\Position;
use Illuminate\Database\Eloquent\Model;

/**
 * Class LicenseTypePosition
 *
 * @package App\Entities\Data
 * @property int $license_type_id
 * @property int $position_id
 * @property int $id
 * @property int $required
 * @property-read \App\Entities\Industry\Position $position
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Entities\Data\State[] $states
 * @mixin \Eloquent
 */
class LicenseTypePosition extends Model
{
    /**
     * @var array
     */
    protected $guarded = [];

    /**
     * @var string
     */
    protected $table = 'license_types_positions';

    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function states()
    {
        return $this->belongsToMany(
            State::class,
            'license_types_positions_states',
            'license_types_position_id',
            'state_id'
        );
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function position()
    {
        return $this->belongsTo(Position::class);
    }
}
