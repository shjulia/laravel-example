<?php

declare(strict_types=1);

namespace App\Listeners\Shift\Support;

use App\Entities\Shift\Shift;
use App\Events\Shift\Support\ProviderRequestedEvent;
use App\Mail\Shift\Support\ProviderRequestedMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

/**
 * Class ProviderRequestedListener
 *
 * Event {@see \App\Events\Shift\Support\ProviderRequestedEvent}
 * @package App\Listeners\Shift\Support
 */
class ProviderRequestedListener implements ShouldQueue
{
    /**
     * @var array
     */
    private $emails;

    /**
     * ProviderRequestedListener constructor.
     * @param array $emails
     */
    public function __construct(array $emails)
    {
        $this->emails = $emails;
    }

    /**
     * @param ProviderRequestedEvent $event
     */
    public function handle(ProviderRequestedEvent $event): void
    {
        /** @var Shift $shift */
        $shift = $event->shift;
        Mail::to($this->emails)->send(new ProviderRequestedMail($shift));
    }
}
