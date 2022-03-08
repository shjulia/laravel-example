<?php

declare(strict_types=1);

namespace App\Entities\Industry;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Speciality
 *
 * @package App\Entities\Industry
 * @property int $id
 * @property string $title
 * @property int $industry_id
 * @property-read \App\Entities\Industry\Industry $industry
 * @mixin \Eloquent
 */
class Speciality extends Model
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
    public function industry()
    {
        return $this->belongsTo(Industry::class);
    }
}
