<?php

declare(strict_types=1);

namespace App\Listeners\Shift;

use App\Entities\Shift\Shift;
use App\Events\Shift\ShiftUpdateEvent;
use App\Mail\Shift\ShiftUpdate;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

/**
 * Class ShiftUpdateListener
 * Notifies that shift was updated.
 *
 * Event {@see \App\Events\Shift\ShiftUpdateEvent}
 * @package App\Listeners\Shift
 */
class ShiftUpdateListener implements ShouldQueue
{
    /**
     * @var array
     */
    private $emails;

    /**
     * ShiftUpdateListener constructor.
     * @param array $emails
     */
    public function __construct(array $emails)
    {
        $this->emails = $emails;
    }

    /**
     * @param ShiftUpdateEvent $event
     */
    public function handle(ShiftUpdateEvent $event): void
    {
        /** @var Shift $shift */
        $shift = $event->shift;
        $action = $event->action;
        $user = $event->user;
        $shift->logs()->create([
            'action' => $action,
            'user_id' => $user ? $user->id : null
        ]);
        if ($event->send) {
            Mail::to($this->emails)->send(new ShiftUpdate($shift, $action, $user));
        }
    }
}
