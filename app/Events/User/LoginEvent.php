<?php

declare(strict_types=1);

namespace App\Events\User;

use App\Entities\User\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Class LoginEvent
 *
 * Listener {@see \App\Listeners\User\LoginListener}
 * @package App\Events\User
 */
class LoginEvent
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
