<?php

declare(strict_types=1);

namespace App\Events\User;

use App\Entities\User\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Class ActionLogEvent
 *
 * Listener {@see \App\Listeners\User\ActionLogListener}
 * @package App\Events\User
 */
class ActionLogEvent
{
    use Dispatchable;
    use SerializesModels;

    /**
     * @var User
     */
    public $user;

    /**
     * @var string
     */
    public $desc;

    /**
     * @var User|null
     */
    public $admin;

    /**
     * ActionLogEvent constructor.
     * @param User $user
     * @param string $desc
     * @param User|null $admin
     */
    public function __construct(User $user, string $desc, ?User $admin = null)
    {
        $this->user = $user;
        $this->desc = $desc;
        $this->admin = $admin;
    }
}
