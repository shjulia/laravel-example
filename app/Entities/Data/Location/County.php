<?php

declare(strict_types=1);

namespace App\Entities\Data\Location;

use Illuminate\Database\Eloquent\Model;

/**
 * Class County - USA counties
 *
 * @package App\Entities\Data\Location
 * @mixin \Eloquent
 * @property int $id
 * @property string $name
 * @property string $state
 * @property float $tier
 * @property float $lat
 * @property float $lng
 */
class County extends Model
{
    /**
     * @inheritdoc
     */
    protected $guarded = [];

    /**
     * @inheritdoc
     */
    public $timestamps = false;
}
