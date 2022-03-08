<?php

namespace App\Jobs\Shift;

use App\Entities\Shift\Shift;
use App\Exceptions\Shift\NoProvidersAreAvailableException;
use App\Repositories\Shift\ShiftRepository;
use App\UseCases\Shift\ShiftService;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

/**
 * Class MatchNowJob
 * @package App\Jobs\Shift
 */
class MatchNowJob implements ShouldQueue
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
     * @var bool
     */
    private $isToday;

    /**
     * @var bool
     */
    private $isCheck;

    /**
     * MatchNowJob constructor.
     * @param Shift $shift
     * @param bool|null $isCheck
     */
    public function __construct(Shift $shift, ?bool $isCheck = false)
    {
        $this->shift = $shift;
        $this->isToday = $shift->date == Carbon::now($shift->creator->tz)->format('Y-m-d');
        $this->isCheck = $isCheck;
    }

    /**
     * @param ShiftService $shiftService
     * @param ShiftRepository $shiftRepository
     */
    public function handle(ShiftService $shiftService, ShiftRepository $shiftRepository): void
    {
        $shift = $shiftRepository->getById($this->shift->id);
        if ($shift->isAcceptedByProviderStatus() || $shift->isCanceledStatus()) {
            return;
        }
        if (!$this->checkTime($shift, $shiftService)) {
            if ($shift->is_floating) {
                $shiftService->findNewProvider($shift);
                return;
            }
            if (!$shift->multi_days || !$shift->successChildrenExists()) {
                $shiftService->noProvidersInTime($shift);
            } elseif ($shift->multi_days && $shift->successChildrenExists()) {
                $shift->setAcceptedByProviderStatus();
                $shift->save();
            }
            return;
        }
        if ($this->isCheck && $shift->potential_provider_id) {
            self::dispatch($this->shift)->delay(now()->addMinutes(1)->addSeconds(30));
            return;
        }
        try {
            $shiftService->match($shift);
        } catch (NoProvidersAreAvailableException $e) {
            self::dispatch($shift)->delay(now()->addMinutes($this->isToday ? 3 : 30));
            return;
        }
        self::dispatch($this->shift, true)->delay(now()->addMinutes(2));
    }

    /**
     * @param Shift $shift
     * @param ShiftService $shiftService
     * @return bool
     */
    private function checkTime(Shift $shift, ShiftService $shiftService): bool
    {
        if (!$shift->multi_days) {
            return $this->checkShiftTime($shift);
        }
        $freeShifts = $shift->freeChildren;
        $isFutureShiftExists = false;
        foreach ($freeShifts as $freeShift) {
            if ($this->checkShiftTime($freeShift)) {
                $isFutureShiftExists = true;
                break;
            }
            $shiftService->noProvidersInTime($freeShift);
        }
        return $isFutureShiftExists;
    }

    /**
     * @param Shift $shift
     * @param string $startDate
     * @return bool
     */
    private function checkShiftTime(Shift $shift): bool
    {
        if ($shift->startsInHours() > 0.25) { //20
            return true;
        }
        return false;
    }
}
