<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Entities\Shift\Shift;
use App\Jobs\Shift\DayBeforeFirstShiftJob;
use App\Mail\Shift\ShiftReminder;
use Illuminate\Console\Command;
use Illuminate\Contracts\Mail\Mailer;

/**
 * Class ShiftStartReminder
 * Sends reminders for the provider about the upcoming shift.
 *
 * @package App\Console\Commands
 */
class ShiftStartReminder extends Command
{
    /**
     * @var string
     */
    protected $signature = 'shifts:reminder';

    /**
     * @var string
     */
    protected $description = 'Sends reminders for the provider about the upcoming shift.';

    /**
     * @var Mailer
     */
    private $mailer;

    public function __construct(Mailer $mailer)
    {
        $this->mailer = $mailer;
        parent::__construct();
    }

    public function handle()
    {
        /** @var Shift[] $shifts */
        $shifts = Shift::where([
            'status' => Shift::STATUS_ACCEPTED_BY_PROVIDER,
            'processed' => 0,
            'multi_days' => 0
        ])->with(['provider', 'practice.users'])->get();

        /** @var Shift $shift */
        foreach ($shifts as $shift) {
            $timeDiff = $shift->startsInHours();
            $provider = $shift->provider;
            $user = $provider->user;
            if (
                $timeDiff <= Shift::REMINDER_STATUS_72
                && $timeDiff > Shift::REMINDER_STATUS_24
                && $shift->reminder_status != Shift::REMINDER_STATUS_72
            ) {
                $this->mailer->to($user->email)->send(new ShiftReminder($user, $shift, Shift::REMINDER_STATUS_72));
                $shift->update([
                    'reminder_status' => Shift::REMINDER_STATUS_72
                ]);
            } elseif (
                $timeDiff <= Shift::REMINDER_STATUS_24
                && $timeDiff > Shift::REMINDER_STATUS_2
                && $shift->reminder_status != Shift::REMINDER_STATUS_24
            ) {
                $this->mailer->to($user->email)->send(new ShiftReminder($user, $shift, Shift::REMINDER_STATUS_24));
                $shift->update([
                    'reminder_status' => Shift::REMINDER_STATUS_24
                ]);
                DayBeforeFirstShiftJob::dispatch($shift, $provider);
            } elseif (
                $timeDiff <= Shift::REMINDER_STATUS_2
                && $shift->reminder_status != Shift::REMINDER_STATUS_2
            ) {
                $this->mailer->to($user->email)->send(new ShiftReminder($user, $shift, Shift::REMINDER_STATUS_2));
                $shift->update([
                    'reminder_status' => Shift::REMINDER_STATUS_2
                ]);
            }
        }
    }
}
