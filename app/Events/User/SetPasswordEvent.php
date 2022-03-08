<?php

namespace App\Events\User;

use App\Entities\User\User;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;

/**
 * Class SetPasswordEvent
 *
 * Listener {@see \App\Listeners\User\SetPasswordListener}
 * @package App\Events\User
 */
class SetPasswordEvent
{
    use Dispatchable;
    use SerializesModels;

    /**
     * @var User
     */
    public $user;

    /**
     * AccountApproved constructor.
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }
}
