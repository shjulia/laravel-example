<?php

declare(strict_types=1);

namespace App\UseCases\Auth;

use App\Entities\User\LoginLog;
use App\Entities\User\User;
use App\Helpers\LogHelper;
use App\Repositories\User\LoginLogRepository;

/**
 * Class LoginLogService
 * Saves logs for last five user logins.
 *
 * @package App\UseCases\Auth
 */
class LoginLogService
{
    /**
     * @var LoginLogRepository
     */
    private $logRepository;

    /**
     * LoginLogService constructor.
     * @param LoginLogRepository $logRepository
     */
    public function __construct(LoginLogRepository $logRepository)
    {
        $this->logRepository = $logRepository;
    }

    /**
     * @param User $user
     */
    public function log(User $user): void
    {
        try {
            $logins = $this->logRepository->findByUser($user);

            if ($logins->count() == 5) {
                /** @var LoginLog $oldLogin */
                $oldLogin = $logins->first();
                $oldLogin->delete();
            }
            LoginLog::create([
                'user_id' => $user->id
            ]);
        } catch (\Exception $e) {
            LogHelper::error($e);
            throw new \DomainException('Login log saving error');
        }
    }
}
