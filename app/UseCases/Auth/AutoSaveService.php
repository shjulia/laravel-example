<?php

declare(strict_types=1);

namespace App\UseCases\Auth;

use App\Entities\User\SignupAutosave;
use App\Entities\User\User;
use Carbon\Carbon;

/**
 * Class AutoSaveService
 * Saves unfinished registrations.
 *
 * @package App\UseCases\Auth
 */
class AutoSaveService
{
    /**
     * @param string $email
     * @param null|string $firstName
     * @param null|string $lastName
     */
    public function save(string $email, ?string $firstName = null, ?string $lastName = null): void
    {
        if (User::where('email', $email)->first()) {
            return;
        }
        SignupAutosave::updateOrCreate(
            ['email' => $email],
            [
                'first_name' => $firstName,
                'last_name' => $lastName,
                'updated_at' => Carbon::createFromTimestamp(time())
            ]
        );
    }
}
