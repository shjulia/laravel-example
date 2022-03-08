<?php

namespace App\Events\User\Provider;

use App\Entities\User\User;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;

/**
 * Class AccountApproved
 *
 * Listener {@see \App\Listeners\User\Provider\AccountApprovedListener}
 * @package App\Events\User
 */
class AccountApproved
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
