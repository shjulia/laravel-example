<?php

declare(strict_types=1);

namespace App\Entities\User\Provider;

use Illuminate\Database\Eloquent\Model;

/**
 * Class ProviderAvailability - Days and time periods of day when provider available to work
 *
 * @package App\Entities\User\Provider
 * @property int $id
 * @property int $day
 * @property int $specialist_id provider id
 * @property string|null $from_hour time in H:i format
 * @property string|null $to_hour time in H:i format
 * @mixin \Eloquent
 */
class ProviderAvailability extends Model
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
