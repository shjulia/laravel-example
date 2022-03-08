<?php

declare(strict_types=1);

namespace App\Entities\Data;

use App\Entities\User\User;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Terms
 *
 * @package App\Entities\Data
 * @property int $id
 * @property string $text
 * @property int|null $admin_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Entities\User\User|null $admin
 * @mixin \Eloquent
 */
class Term extends Model
{
    /**
     * @var array
     */
    protected $guarded = [];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id', 'id');
    }
}
