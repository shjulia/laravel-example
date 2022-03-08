<?php

declare(strict_types=1);

namespace App\Entities\User;

use Illuminate\Database\Eloquent\Model;

/**
 * Class LoginLog -  all user log-in data
 * @package App\Entities\User
 * @mixin \Eloquent
 */
class LoginLog extends Model
{
    /**
     * @var array
     */
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
