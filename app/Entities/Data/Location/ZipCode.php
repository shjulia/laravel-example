<?php

declare(strict_types=1);

namespace App\Entities\Data\Location;

use Illuminate\Database\Eloquent\Model;

/**
 * Class ZipCode - USA zip codes
 *
 * @package App\Entities\Data\Location
 * @mixin \Eloquent
 * @property int $id
 * @property string $zip
 * @property string $place_name
 * @property string $state_code
 * @property string $county
 * @property float $lat
 * @property float $lng
 */
class ZipCode extends Model
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
