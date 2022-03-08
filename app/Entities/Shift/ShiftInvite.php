<?php

declare(strict_types=1);

namespace App\Entities\Shift;

use App\Entities\User\Provider\Specialist;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ShiftInvite - Shift invites log with invite statuses
 *
 * @package App\Entities\Shift
 * @property int $id
 * @property int $shift_id
 * @property int $provider_id
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Entities\User\Provider\Specialist $provider
 * @property-read \App\Entities\Shift\Shift $shift
 * @mixin \Eloquent
 */
class ShiftInvite extends Model
{
    /**
     * @var array
     */
    protected $guarded = [];

    /** @var string */
    public const ACCEPTED = 'accepted';

    /** @var string */
    public const DECLINED = 'declined';

    /** @var string */
    public const NO_RESPOND = 'no respond';

    /** @var string */
    public const VIEWED = 'viewed';

    /**
     * @param Shift $shift
     * @param Specialist $provider
     * @return static
     */
    public static function newInvite(Shift $shift, Specialist $provider): self
    {
        if (!$shift->isMatchingStatus() && !$shift->isParentMatchingStatus()) {
            throw new \DomainException('Shift doesn\'t have matching status now');
        }
        $invite = new self();
        $invite->shift_id = $shift->id;
        $invite->provider_id = $provider->user_id;
        $invite->setBaseStatus();
        return $invite;
    }

    public function accept(): void
    {
        $this->setAcceptedStatus();
    }

    public function decline(): void
    {
        $this->setDeclinedStatus();
    }

    private function setBaseStatus(): void
    {
        $this->status = self::NO_RESPOND;
    }

    private function setAcceptedStatus(): void
    {
        $this->status = self::ACCEPTED;
    }

    private function setDeclinedStatus(): void
    {
        $this->status = self::DECLINED;
    }

    public function setViewedStatus(): void
    {
        $this->status = self::VIEWED;
    }

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
    public function provider()
    {
        return $this->belongsTo(Specialist::class, 'provider_id', 'user_id');
    }

    /**
     * @return bool
     */
    public function isAccepted(): bool
    {
        return $this->status === self::ACCEPTED;
    }

    /**
     * @return bool
     */
    public function isDeclined(): bool
    {
        return $this->status === self::DECLINED;
    }

    /**
     * @return bool
     */
    public function isNoRespond(): bool
    {
        return  $this->status === self::NO_RESPOND;
    }

    /**
     * @return bool
     */
    public function isViewed(): bool
    {
        return  $this->status === self::VIEWED;
    }
}
