<?php

declare(strict_types=1);

namespace App\Listeners\User;

use App\Entities\User\ApproveLog;
use App\Events\User\ActionLogEvent;
use Illuminate\Contracts\Queue\ShouldQueue;

/**
 * Class ActionLogListener
 * Writes down information about who and how have changed user's approval status.
 *
 * Event {@see \App\Events\User\ActionLogEvent}
 * @package App\Listeners\User
 */
class ActionLogListener implements ShouldQueue
{
    /**
     * @param ActionLogEvent $event
     */
    public function handle(ActionLogEvent $event): void
    {
        $event->user->approveLogs()->create([
            'status' => $event->admin ? ApproveLog::CHANGED_BY_ADMIN : ApproveLog::CHANGED_BY_USER,
            'desc' => $event->desc,
            'admin_id' => $event->admin ? $event->admin->id : null
        ]);
    }
}
