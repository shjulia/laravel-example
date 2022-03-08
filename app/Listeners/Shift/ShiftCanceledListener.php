<?php

declare(strict_types=1);

namespace App\Listeners\Shift;

use App\Entities\Shift\Shift;
use App\Entities\User\User;
use App\Events\Shift\ShiftCanceledEvent;
use App\Notifications\Shift\ShiftCanceledNotification;

/**
 * Class ShiftCanceledListener
 * Notifies that shift was cancelled.
 *
 * Event {@see \App\Events\Shift\ShiftCanceledEvent}
 * @package App\Listeners\Shift
 */
class ShiftCanceledListener
{
    /**
     * @param ShiftCanceledEvent $event
     */
    public function handle(ShiftCanceledEvent $event): void
    {
        /** @var User $user */
        $user = $event->shift->provider->user;
        /** @var Shift $shift */
        $shift = $event->shift;
        $user->notify(new ShiftCanceledNotification($shift));
    }
}
