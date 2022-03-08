<?php

declare(strict_types=1);

namespace App\Jobs\Shift;

use App\Entities\Notification\EmailMark;
use App\Entities\Shift\Shift;
use App\Entities\User\Provider\Specialist;
use App\Entities\User\User;
use App\Mail\Shift\HowPaymentWorksMail;
use App\Repositories\Notification\EmailMarkRepository;
use App\Repositories\Shift\ShiftRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class DayBeforeFirstShiftJob implements ShouldQueue
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
     * @var Specialist
     */
    private $provider;

    /**
     * DayBeforeFirstShiftJob constructor.
     * @param Shift $shift
     * @param Specialist $provider
     */
    public function __construct(Shift $shift, Specialist $provider)
    {
        $this->shift = $shift;
        $this->provider = $provider;
    }

    public function handle(ShiftRepository $shiftRepository, EmailMarkRepository $emailMarkRepository)
    {
        if (!$shiftRepository->isFirstShiftForProvider($this->provider, $this->shift)) {
            return;
        }

        $user = User::find($this->provider->id);
        if ($emailMarkRepository->wasEmailSent($user, EmailMark::HOW_PAYMENT_WORKS)) {
            return;
        }

        if ($this->shift->startsInHours() <= 24) {
            try {
                Mail::to($this->shift->creator->email)->send(new HowPaymentWorksMail());
                $emailMark = EmailMark::createMark($user, EmailMark::HOW_PAYMENT_WORKS);
                $emailMark->saveOrFail();
            } catch (\Throwable $e) {
                \LogHelper::error($e);
            }
        }
    }
}
