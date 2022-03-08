<?php

declare(strict_types=1);

namespace App\Console\Commands\Shift;

use App\Entities\Shift\Shift;
use App\UseCases\Shift\ShiftService;
use Carbon\Carbon;
use Illuminate\Console\Command;

/**
 * Class StartMatchingByDate
 * Starts matching if it is 3 days left before shift starts.
 *
 * @package App\Console\Commands\Shift
 */
class StartMatchingByDate extends Command
{
    /**
     * @var string
     */
    protected $signature = 'shifts:start-matching';

    /**
     * @var string
     */
    protected $description = 'Start matching';

    /**
     * @var ShiftService
     */
    private $service;

    /**
     * StartMatchingByDate constructor.
     * @param ShiftService $service
     */
    public function __construct(ShiftService $service)
    {
        $this->service = $service;
        parent::__construct();
    }

    public function handle()
    {
        $date72h = Carbon::now()->addDays(3)->format('Y-m-d');
        $shifts = Shift::where([
            'status' => Shift::STATUS_WAITING,
            ['date', $date72h]
        ])
            ->with('practice.users')
            ->get();
        foreach ($shifts as $shift) {
            $time = Carbon::now($shift->creator->tz)->format('H:i');
            if ($time < $shift->from_time) {
                continue;
            }
            try {
                $this->service->startMatching($shift);
            } catch (\Exception $e) {
                \LogHelper::error($e, ['userId' => $shift->id]);
            }
        }
    }
}
