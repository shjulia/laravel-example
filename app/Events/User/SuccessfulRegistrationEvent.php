<?php

namespace App\Events\User;

use App\Entities\User\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Class SuccessfulRegistrationEvent
 *
 * Listener {@see \App\Listeners\User\SuccessfulRegistrationListener}
 * @package App\Events\User
 */
class SuccessfulRegistrationEvent
{
    use Dispatchable;
    use SerializesModels;

    /** @var User $user */
    public $user;

    /** @var string $link */
    public $link;

    /**
     * SuccessfulRegistrationEvent constructor.
     * @param User $user
     * @param null $link
     */
    public function __construct(User $user, $link = null)
    {
        $this->user = $user;
        $this->link = $link;
    }
}
