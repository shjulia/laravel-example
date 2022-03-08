<?php

declare(strict_types=1);

namespace App\Entities\Shift;

use App\Entities\User\User;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ShiftLog - Log of all shift actions
 *
 * @package App\Entities\Shift
 * @property int $id
 * @property int $shift_id
 * @property string $action
 * @property int|null $user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Entities\Shift\Shift $shift
 * @property-read \App\Entities\User\User|null $user
 * @mixin \Eloquent
 */
class ShiftLog extends Model
{
    /**
     * @var array
     */
    protected $guarded = [];

    public const MATCHING_STARTED = 'Matching started';
    public const PROVIDER_ACCEPTED = 'Provider accepted shift';

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function shift()
    {
        return $this->belongsTo(Shift::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * @return bool
     */
    public function isMatchingStartedRecord(): bool
    {
        return $this->action === self::MATCHING_STARTED;
    }

    /**
     * @return bool
     */
    public function isProviderAcceptedRecord(): bool
    {
        return $this->action === self::PROVIDER_ACCEPTED;
    }
}
