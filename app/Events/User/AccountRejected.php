<?php

declare(strict_types=1);

namespace App\Events\User;

use App\Entities\User\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Class AccountRejected
 *
 * Listener {@see \App\Listeners\User\AccountRejectedListener}
 * @package App\Events\User
 */
class AccountRejected
{
    use Dispatchable;
    use SerializesModels;

    /**
     * @var User
     */
    public $user;

    /**
     * AccountRejected constructor.
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }
}
