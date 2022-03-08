<?php

declare(strict_types=1);

namespace App\Jobs\Shift;

use App\Entities\DTO\SmsDTO;
use App\Entities\Shift\Shift;
use App\Entities\Shift\ShiftInvite;
use App\Entities\User\User;
use App\Notifications\SmsNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Class InviteByTextJob
 * @package App\Jobs\Shift
 */
class InviteByTextJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * @var Shift
     */
    private $shift;

    /**
     * @var User
     */
    private $user;

    /**
     * InviteByTextJob constructor.
     * @param Shift $shift
     * @param User $user
     */
    public function __construct(Shift $shift, User $user)
    {
        $this->shift = $shift;
        $this->user = $user;
    }

    public function handle()
    {
        $shift = $this->shift;
        if ($shift->isHasProvider() || $shift->isCanceledStatus() || $shift->isNoPrividerFoundStatus()) {
            return;
        }
        /** @var ShiftInvite|null $invite */
        $invite = $shift->shiftInvites()->where('provider_id', $this->user->id)->first();
        if (!$invite || !$invite->isNoRespond()) {
            return;
        }

        $text = $this->getText($shift);

        $this->user->notify(new SmsNotification(new SmsDTO(
            $text,
            route('shifts.provider.acceptPage', ['shift' => $shift])
        )));
    }

    private function getText(Shift $shift): string
    {
        $lunchText = '';
        if ($shift->lunch_break > 0) {
            $lunchText = " (including " . $shift->lunch_break . "min. lunch break)";
        }

        if (!$shift->multi_days) {
            $text = "You've been matched for a shift with Boon! A practice in "
                . $shift->practice_location->city . ' ' . $shift->practice_location->state
                . " needs a " . $shift->position->title . " on "
                . $shift->period() . $lunchText . " and will pay you $" . $shift->cost_without_surge
                . ($shift->bonuses ? (" plus $" . $shift->bonuses . " bonus.") : ". ")
                . "This shift is time sensitive and will be filled on a first come, first served basis. "
                . "Click here to learn more and accept. ";
        } else {
            $text = "You've been matched for a multi-day shift with Boon! A practice in "
                . $shift->practice_location->city . ' ' . $shift->practice_location->state
                . " needs a " . $shift->position->title . " on "
                . $shift->period() . $lunchText
                . " This shift is time sensitive and will be filled on a first come, first served basis. "
                . "Click to respond to work in part it in whole ";
        }

        return $text;
    }
}
