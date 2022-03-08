<?php

declare(strict_types=1);

namespace App\Jobs\Shift;

use App\Entities\Shift\Shift;
use App\Mail\Shift\Cost\ChangedForPractice;
use App\Mail\Shift\Cost\ChangedForProvider;
use App\Repositories\Shift\ShiftRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Mail\Mailer;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Class CostChangesJob
 * @package App\Jobs\Shift
 */
class CostChangesJob implements ShouldQueue
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
     * @var array
     */
    private $oldCost;

    /**
     * CostChangesJob constructor.
     * @param Shift $shift
     * @param array $oldCost
     */
    public function __construct(Shift $shift, array $oldCost)
    {
        $this->shift = $shift;
        $this->oldCost = $oldCost;
    }

    public function handle(ShiftRepository $shiftRepository, Mailer $mailer)
    {
        $shift = $shiftRepository->getByIdOnlyShift($this->shift->id);
        if (
            $this->oldCost['costForPractice']
            && ($shift->cost_for_practice != $this->oldCost['costForPractice'])
        ) {
            $mailer->to($shift->creator->email)->send(new ChangedForPractice($shift, $this->oldCost));
        }
        if (
            $shift->isHasProvider()
            && $this->oldCost['cost']
            && ($shift->cost != $this->oldCost['cost'])
        ) {
            $mailer->to($shift->provider->user->email)->send(new ChangedForProvider($shift));
        }
    }
}
