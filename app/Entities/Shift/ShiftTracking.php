<?php

declare(strict_types=1);

namespace App\Entities\Shift;

use Illuminate\Database\Eloquent\Model;

/**
 * Class ShiftTracking - Tracking shift start and finish
 *
 * @package App\Entities\Shift
 * @property int $id
 * @property int $shift_id
 * @property string $action
 * @property float|null $lat
 * @property float|null $lng
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Entities\Shift\Shift $shift
 * @mixin \Eloquent
 */
class ShiftTracking extends Model
{
    /**
     * @var array
     */
    protected $guarded = [];

    /**
     * @var string
     */
    protected $table = 'shift_tracking';

    public const ACTION_STARTED = 'started';
    public const ACTION_FINISHED = 'finished';

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function shift()
    {
        return $this->belongsTo(Shift::class);
    }

    public function setStartedAction(): void
    {
        $this->action = self::ACTION_STARTED;
    }

    public function setFinishedAction(): void
    {
        $this->action = self::ACTION_FINISHED;
    }

    /**
     * @param Shift $shift
     * @param bool $isStart
     * @param float|null $lat
     * @param float|null $lng
     * @return static
     */
    public static function createTrack(Shift $shift, bool $isStart, ?float $lat, ?float $lng): self
    {
        $track = new self();
        $track->shift_id = $shift->id;
        $track->lat = $lat;
        $track->lng = $lng;
        if ($isStart) {
            $track->setStartedAction();
        } else {
            $track->setFinishedAction();
        }
        return $track;
    }
}
