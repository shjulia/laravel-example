<?php

declare(strict_types=1);

namespace App\Console\Commands\Shift;

use App\Entities\Shift\Shift;
use App\Jobs\Shift\Provider\AlmostStartedJob;
use Illuminate\Console\Command;

/**
 * Class FindAlmostStartedShifts
 * Finds almost started shifts (less then 15 minutes before shift starts) and sens notification to Provider.
 *
 * @package App\Console\Commands\Shift
 */
class FindAlmostStartedShifts extends Command
{
    /**
     * @var string
     */
    protected $signature = 'shifts:almost-started';

    /**
     * @var string
     */
    protected $description = 'Find almost started shifts';

    public function handle()
    {
        $today = date('Y-m-d');

        /** @var Shift[] $shifts */
        $shifts = Shift::where([
            'status' => Shift::STATUS_ACCEPTED_BY_PROVIDER,
            'multi_days' => 0,
            ['end_date', '<=', $today],
            'processed' => 0
        ])
            ->with('practice.users')
            ->get();

        foreach ($shifts as $shift) {
            $startsIn = $shift->startsInHours();
            if (!($startsIn <= 0.25 && $startsIn >= 0.17)) {
                continue;
            }
            AlmostStartedJob::dispatch($shift);
        }
    }
}
