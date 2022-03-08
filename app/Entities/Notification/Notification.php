<?php

declare(strict_types=1);

namespace App\Entities\Notification;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Notification - Notification shown to users in app
 *
 * @package App\Entities\Notification
 * @mixin \Eloquent
 * @property int $id
 * @property string $title
 * @property string|null $text
 * @property string|null $link
 * @property int $user
 * @property int|null $from
 * @property string|null $icon
 * @property string|null $read_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class Notification extends Model
{
    /** @inheritdoc */
    protected $guarded = [];
}
