<?php

declare(strict_types=1);

namespace App\Repositories\User;

use App\Entities\User\LoginLog;
use App\Entities\User\User;

class LoginLogRepository
{
    /**
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function findAllWithPagination()
    {
        return LoginLog::with('user')->orderBy('id', 'desc')->paginate(20);
    }

    /**
     * @param User $user
     * @return LoginLog[]|\Illuminate\Database\Eloquent\Collection
     */
    public function findByUser(User $user)
    {
        return LoginLog::where('user_id', $user->id)->orderBy('id', 'desc')->get();
    }
}
