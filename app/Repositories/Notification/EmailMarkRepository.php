<?php

declare(strict_types=1);

namespace App\Repositories\Notification;

use App\Entities\Notification\EmailMark;
use App\Entities\User\User;

class EmailMarkRepository
{
    /**
     * @param User $user
     * @param string $type
     * @return bool
     */
    public function wasEmailSent(User $user, string $type): bool
    {
        return EmailMark::where('user_id', $user->id)->where('type', $type)->exists();
    }
}
