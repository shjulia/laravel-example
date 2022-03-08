<?php

namespace App\Console\Commands;

use App\Entities\Shift\Shift;
use App\Events\Shift\AlmostFinishedShiftEvent;
use App\Jobs\Shift\FinishShiftJob;
use App\UseCases\Shift\FinishService;
use Carbon\Carbon;
use Illuminate\Console\Command;

/**
 * Class FindFinishedShifts
 * Finds finished shifts.
 *
 * @package App\Console\Commands
 */
class FindFinishedShifts extends Command
{
    /**
     * @var string
     */
    protected $signature = 'shifts:finished';

    /**
     * @var string
     */
    protected $description = 'Finds shifts finished shifts';

    public function handle()
    {
        $today = date('Y-m-d');
        /** @var Shift[] $shifts */
        $shifts = Shift::where([
            'status' => Shift::STATUS_ACCEPTED_BY_PROVIDER,
            ['end_date', '<=', $today],
            'processed' => 0
        ])
            ->with('practice')
            ->with('provider')
            ->with('practice.users')
            ->get();

        foreach ($shifts as $shift) {
            if ($shift->endsInHours() > 0.25) {
                continue;
            }
            if (!$shift->notified_finish) {
                event(new AlmostFinishedShiftEvent($shift));
                continue;
            }
            if ($shift->endsInHours() <= -1) {
                FinishShiftJob::dispatch($shift);
            }
        }
    }
}
