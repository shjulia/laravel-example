<?php

declare(strict_types=1);

namespace App\Entities\Notification;

use App\Entities\User\User;
use Illuminate\Database\Eloquent\Model;

/**
 * Class EmailMark - Mark about sending console notifications to user.
 * It prevent several times notification sending.
 *
 * @package App\Entities\Notification
 * @mixin \Eloquent
 * @property int $id
 * @property int $user_id
 * @property string $type
 * @property int|null $entity_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Entities\User\User $user
 */
class EmailMark extends Model
{
    /**
     * @var array
     */
    protected $guarded = [];

    /** @var string */
    public const HIRE_FIRST_PROVIDER = 'hire first provider';
    /** @var string */
    public const MISSING_OUT_SHIFTS = 'missing out shifts';
    /** @var string */
    public const HOW_PAYMENT_WORKS = 'how payment works';
    /** @var string */
    public const FEEDBACK_IS_IMPORTANT_12 = 'feedback is important (after 12 hrs)';
    /** @var string */
    public const FEEDBACK_IS_IMPORTANT_WEEK = 'feedback is important (after a week)';
    /** @var string */
    public const HIRE_50_OFF = 'hire first provider 50 off';
    /** @var string */
    public const ROAD_WARRIOR = 'road warrior';
    /** @var string */
    public const AFTER_SHIFT_21_DAYS = '21 days after last shift';
    /** @var string */
    public const AFTER_SHIFT_35_DAYS = '35 days after last shift';
    /** @var string */
    public const AFTER_SHIFT_56_DAYS = '56 days after last shift';
    /** @var string */
    public const TEMP_NOT_ELIGIBLE = 'temp not eligible';
    /** @var string */
    public const UPLOAD_PROFILE_PICTURE = 'upload profile picture';

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    /**
     * @param User $user
     * @param string $type
     * @param int|null $entityId
     * @return static
     */
    public static function createMark(User $user, string $type, ?int $entityId = null): self
    {
        $mark = new self();
        $mark->user_id = $user->id;
        $mark->type = $type;
        $mark->entity_id = $entityId;
        return $mark;
    }
}
