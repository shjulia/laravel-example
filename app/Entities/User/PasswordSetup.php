<?php

declare(strict_types=1);

namespace App\Entities\User;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class PasswordSetup
 * @package App\Entities\User
 */
class PasswordSetup extends Model
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
     * @var string
     */
    protected $primaryKey = 'user_id';

    /**
     * @var array
     */
    protected $dates = ['created_date'];

    public function setIdAttribute()
    {
        return $this->user_id;
    }

    /**
     * @param User $user
     */
    public function setUser(User $user): void
    {
        $this->user_id = $user->id;
        $this->created_date = Carbon::now();
    }
}
