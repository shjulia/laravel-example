<?php

namespace App\Listeners\Shift;

use App\Entities\Shift\Shift;
use App\Events\Shift\ProvidersNotFoundEvent;
use App\Mail\Shift\ProvidersNotFoundMail;
use App\Repositories\User\UserRepository;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

/**
 * Class ProvidersNotFoundListener
 * Sends email if no providers were found for shift.
 *
 * Event {@see \App\Events\Shift\ProvidersNotFoundEvent}
 * @package App\Listeners\Shift
 */
class ProvidersNotFoundListener implements ShouldQueue
{
    /**
     * @var array
     */
    private $emails;

    /**
     * ProvidersNotFoundListener constructor.
     * @param array $emails
     */
    public function __construct(array $emails)
    {
        $this->emails = $emails;
    }

    /**
     * @param ProvidersNotFoundEvent $event
     */
    public function handle(ProvidersNotFoundEvent $event): void
    {
        /** @var Shift $shift */
        $shift = $event->shift;
        Mail::to($this->emails)->send(new ProvidersNotFoundMail($shift));
    }
}
