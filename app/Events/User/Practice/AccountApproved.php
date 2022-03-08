<?php

declare(strict_types=1);

namespace App\Events\User\Practice;

use App\Entities\User\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Class AccountApproved
 *
 * Event {@see \App\Listeners\User\Practice\AccountApprovedListener}
 * @package App\Events\User\Practice
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
