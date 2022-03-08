<?php

declare(strict_types=1);

namespace App\Entities\User;

use Illuminate\Database\Eloquent\Model;

/**
 * Class ApproveLog - all user account changes
 *
 * @package App\Entities\User
 * @property int $id
 * @property int $user_id
 * @property int|null $admin_id
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $desc
 * @property-read \App\Entities\User\User $admin
 * @property-read \App\Entities\User\User $user
 * @mixin \Eloquent
 */
class ApproveLog extends Model
{
    /**
     * @var array
     */
    protected $guarded = [];

    /** @var string */
    public const CHANGED_TO_APPROVED = "changed to approved";
    /** @var string */
    public const CHANGED_TO_WAITING = "changed to waiting";
    /** @var string */
    public const CHANGED_BY_USER = "changed by user";
    /** @var string */
    public const CHANGED_BY_ADMIN = "changed by admin";
    /** @var string */
    public const REGISTRATION_FINISHED_PROVIDER = "SSN saved";
    /** @var string */
    public const REGISTRATION_FINISHED_PRACTICE = "Insurance saved";

    /**
     * If action did by user
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    /**
     * If action did by admin
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function admin()
    {
        return $this->hasOne(User::class, 'id', 'admin_id');
    }

    /**
     * @return bool
     */
    public function isApproved(): bool
    {
        return $this->status === self::CHANGED_TO_APPROVED;
    }

    /**
     * @return bool
     */
    public function isWaiting(): bool
    {
        return $this->status === self::CHANGED_TO_WAITING;
    }

    /**
     * @return bool
     */
    public function isChangedByUser(): bool
    {
        return $this->status === self::CHANGED_BY_USER;
    }

    /**
     * @return bool
     */
    public function isChangedByAdmin(): bool
    {
        return $this->status === self::CHANGED_BY_ADMIN;
    }

    /**
     * @return bool
     */
    public function isFinishedStep(): bool
    {
        return in_array($this->desc, [
            self::REGISTRATION_FINISHED_PRACTICE,
            self::REGISTRATION_FINISHED_PROVIDER
        ]);
    }
}
