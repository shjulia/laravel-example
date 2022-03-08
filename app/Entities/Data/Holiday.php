<?php

declare(strict_types=1);

namespace App\Entities\Data;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Holiday - USA holidays
 *
 * @package App\Entities\Data
 * @property int $id
 * @property string $title
 * @mixin \Eloquent
 */
class Holiday extends Model
{
    /**
     * @var array
     */
    protected $guarded = [];

    /**
     * @var bool
     */
    public $timestamps = false;
}
