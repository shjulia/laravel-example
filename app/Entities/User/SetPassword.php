<?php

declare(strict_types=1);

namespace App\Entities\User;

use Illuminate\Database\Eloquent\Model;

/**
 * Class SetPassword
 *
 * @package App\Entities\User
 * @property int $id
 * @property string $email
 * @property string $token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @mixin \Eloquent
 */
class SetPassword extends Model
{
    /**
     * @var array
     */
    protected $guarded = [];

    /**
     * @param string $token
     */
    public function setToken(string $token)
    {
        $this->token = md5($token);
    }
}
