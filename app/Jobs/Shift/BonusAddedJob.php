<?php

declare(strict_types=1);

namespace App\Jobs\Shift;

use App\Entities\DTO\SmsDTO;
use App\Entities\Shift\Shift;
use App\Entities\Shift\ShiftInvite;
use App\Notifications\SmsNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Class BonusAddedJob
 * @package App\Jobs\Shift
 */
class BonusAddedJob implements ShouldQueue
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
     * BonusAddedJob constructor.
     * @param Shift $shift
     */
    public function __construct(Shift $shift)
    {
        $this->shift = $shift;
    }

    public function handle()
    {
        $shift = $this->shift;

        $text = $this->getFullText($shift);

        if ($shift->isHasProvider()) {
            $shift->provider->user->notify(new SmsNotification(new SmsDTO($text)));
            return;
        }
        if (!$shift->multi_days) {
            $text .= " Click here to accept!";
            $this->notifyNoRespondInvites($shift, $text);
            return;
        } else {
            /** @var Shift $child */
            foreach ($shift->children as $child) {
                if (!$child->isAcceptedByProviderStatus()) {
                    continue;
                }
                $text = $this->getFullText($child);
                $child->provider->user->notify(new SmsNotification(new SmsDTO($text)));
            }
            $text = $this->getEachDayText($shift);
            $this->notifyNoRespondInvites($shift, $text);
        }
    }

    /**
     * @param Shift $shift
     * @param string $text
     */
    private function notifyNoRespondInvites(Shift $shift, string $text): void
    {
        $invites = $shift->shiftInvites()->where('status', ShiftInvite::NO_RESPOND)->with(['provider.user'])->get();
        foreach ($invites as $invite) {
            $invite->provider->user->notify(new SmsNotification(new SmsDTO(
                $text,
                route('shifts.provider.acceptPage', ['shift' => $shift])
            )));
        }
    }

    /**
     * @param Shift $shift
     * @return string
     */
    private function getFullText(Shift $shift): string
    {
        return "A bonus of $" . $shift->bonuses
            . " has been added to the shift in " . $shift->practice_location->shortAddress() . " on " . $shift->period()
            . " for a total of "
            . $shift->cost_without_surge . ($shift->bonuses ? (" plus $" . $shift->bonuses . " bonus.") : ". ");
    }

    /**
     * @param Shift $shift
     * @return string
     */
    private function getEachDayText(Shift $shift): string
    {
        return "A bonus of $" . ($shift->bonuses / $shift->multi_days)
            . " has been added to each day of the shift in " . $shift->practice_location->shortAddress()
            . " on " . $shift->period()
            . " Click here to accept!";
    }
}
