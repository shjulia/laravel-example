<?php

declare(strict_types=1);

namespace App\Entities\User;

use App\Entities\Invite\Invite;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Referral
 *
 * @package App\Entities\User
 * @property int $user_id
 * @property string $referral_code
 * @property int $referred_amount
 * @property float $referral_money_earned
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Entities\Invite\Invite[] $invites
 * @property-read \App\Entities\User\User $user
 * @mixin \Eloquent
 */
class Referral extends Model
{
    /**
     * Payment that user will get for success referral shift
     * @var int
     */
    public const REFERRAL_FEE = 100;

    /**
     * Has relation one to one with User
     *
     * @var string
     */
    protected $primaryKey = "user_id";

    /**
     * @var array
     */
    protected $guarded = [];

    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    /**
     * Relation with all invites (accepted and not responded)
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function invites()
    {
        return $this->hasMany(Invite::class, 'referral_id', 'user_id');
    }
}
