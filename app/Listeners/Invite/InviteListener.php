<?php

namespace App\Listeners\Invite;

use App\Entities\Invite\Invite;
use App\Events\Invite\InviteEvent;
use App\Mail\Invite\InviteMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

/**
 * Class InviteListener
 *
 * Event {@see \App\Events\Invite\InviteEmail}
 * @package App\Listeners\Invite
 */
class InviteListener implements ShouldQueue
{
    /**
     * @param InviteEvent $event
     */
    public function handle(InviteEvent $event): void
    {
        /** @var Invite $invite */
        $invite = $event->invite;
        Mail::to($invite->email)->send(new InviteMail($invite));
    }
}
