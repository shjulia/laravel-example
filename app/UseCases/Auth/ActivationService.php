<?php

declare(strict_types=1);

namespace App\UseCases\Auth;

use App\Entities\User\User;
use Illuminate\Support\Facades\DB;

/**
 * Class ActivationService
 * Allows activate and deactivate user.
 *
 * @package App\UseCases\Auth
 */
class ActivationService
{
    /**
     * @param User $user
     * @throws \Exception
     */
    public function deactivate(User $user): void
    {
        $user->deactivate();
        try {
            DB::beginTransaction();
            $user->saveOrFail();

            if ($user->isProvider()) {
                $provider = $user->specialist;
                $provider->setUnavailable();
                $provider->saveOrFail();
            }
            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            \LogHelper::error($e, ['userId' => $user->id]);
            throw new \DomainException('Deactivating account error.');
        }
    }

    /**
     * @param User $user
     * @throws \Exception
     */
    public function activate(User $user): void
    {
        $user->activate();

        try {
            DB::beginTransaction();
            $user->saveOrFail();
            if ($user->isProvider()) {
                $provider = $user->specialist;
                $provider->setAvailable();
                $provider->saveOrFail();
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            \LogHelper::error($e, ['userId' => $user->id]);
            throw new \DomainException('Activating account error.');
        }
    }
}
