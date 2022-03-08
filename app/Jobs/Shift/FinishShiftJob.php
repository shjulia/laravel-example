<?php

declare(strict_types=1);

namespace App\Jobs\Shift;

use App\Entities\Shift\Shift;
use App\UseCases\Shift\FinishService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Class FinishShiftJob
 * @package App\Jobs\Shift
 */
class FinishShiftJob implements ShouldQueue
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
     * FinishShiftJob constructor.
     * @param Shift $shift
     */
    public function __construct(Shift $shift)
    {
        $this->shift = $shift;
    }

    /**
     * @param FinishService $finishService
     * @throws \Exception
     */
    public function handle(FinishService $finishService)
    {
        $finishService->finish($this->shift);
    }
}
