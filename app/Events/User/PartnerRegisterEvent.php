<?php

namespace App\Events\User;

use App\Entities\User\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Class PartnerRegisterEvent
 *
 * Listener {@see \App\Listeners\User\PartnerRegisterListener}
 * @package App\Events\User
 */
class PartnerRegisterEvent
{
    use Dispatchable;
    use SerializesModels;

    /** @var User $user */
    public $user;

    /**
     * PartnerRegisterEvent constructor.
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }
}
