<?php

declare(strict_types=1);

namespace App\Events\Invite;

use App\Entities\Invite\Invite;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Class InviteEvent
 *
 * Listener {@see \App\Listeners\Invite\InviteListener}
 * @package App\Events\Invite
 */
class InviteEvent
{
    use Dispatchable;
    use SerializesModels;

    /**
     * @var Invite
     */
    public $invite;

    /**
     * InviteEvent constructor.
     * @param Invite $invite
     */
    public function __construct(Invite $invite)
    {
        $this->invite = $invite;
    }
}
