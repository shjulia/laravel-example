<?php

declare(strict_types=1);

namespace App\Jobs\Shift;

use App\Entities\Notification\EmailMark;
use App\Entities\Shift\Shift;
use App\Entities\User\Practice\Practice;
use App\Mail\Shift\FirstProviderMail;
use App\Repositories\Shift\ShiftRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class FirstProviderRequestJob implements ShouldQueue
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
     * FirstProviderRequestJob constructor.
     * @param Shift $shift
     */
    public function __construct(Shift $shift)
    {
        $this->shift = $shift;
    }

    public function handle(ShiftRepository $shiftRepository)
    {
        /** @var Shift $shift */
        $shift = $this->shift;
        /** @var Practice $practice */
        $practice = $shift->practice;

        if ($shiftRepository->isFirstShiftForPractice($practice, $shift)) {
            Mail::to($shift->creator->email)->send(new FirstProviderMail());
        }
    }
}
