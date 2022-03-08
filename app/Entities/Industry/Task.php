<?php

declare(strict_types=1);

namespace App\Entities\Industry;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Task - Provider routine tasks.
 * Provider can select routine tasks in his account. Also practice can request shift with tasks.
 *
 * @package App\Entities\Industry
 * @property int $id
 * @property string $title
 * @property int $position_id
 * @property-read \App\Entities\Industry\Position $position
 * @mixin \Eloquent
 */
class Task extends Model
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
    public function position()
    {
        return $this->belongsTo(Position::class);
    }
}
