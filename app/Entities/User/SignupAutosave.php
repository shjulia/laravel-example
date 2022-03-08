<?php

declare(strict_types=1);

namespace App\Entities\User;

use Illuminate\Database\Eloquent\Model;

/**
 * Class SignupAutosave - class for store users who break signup on first step
 *
 * @package App\Entities\User
 * @property int $id
 * @property string $email
 * @property string|null $first_name
 * @property string|null $last_name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @mixin \Eloquent
 */
class SignupAutosave extends Model
{
    /**
     * @var array
     */
    protected $guarded = [];
}
