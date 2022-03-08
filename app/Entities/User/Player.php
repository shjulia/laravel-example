<?php

declare(strict_types=1);

namespace App\Entities\User;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Player - webpush subcriber
 *
 * @package App\Entities\User
 * @mixin \Eloquent
 * @property int $id
 * @property int $user_id
 * @property string $player_id
 */
class Player extends Model
{
    /** @inheritdoc */
    protected $guarded = [];

    /** @inheritdoc */
    public $timestamps = false;
}
