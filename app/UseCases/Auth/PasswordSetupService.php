<?php

declare(strict_types=1);

namespace App\UseCases\Auth;

use App\Entities\User\PasswordSetup;
use App\Entities\User\User;

/**
 * Class PasswordSetupService
 * Saves users who set up password
 *
 * @package App\UseCases\Auth
 */
class PasswordSetupService
{
    /**
     * @param User $user
     */
    public function saveSetup(User $user): void
    {
        try {
            if ($setup = PasswordSetup::where('user_id', $user->id)->first()) {
                $setup->delete();
            }

            $setup = new PasswordSetup();
            $setup->setUser($user);
            $setup->save();
        } catch (\Exception $e) {
            \LogHelper::error($e);
            throw new \DomainException('Password saving error');
        }
    }
}
