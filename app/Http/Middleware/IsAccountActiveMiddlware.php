<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Entities\User\User;
use Closure;
use Illuminate\Support\Facades\Auth;

class IsAccountActiveMiddlware
{
    public function handle($request, Closure $next)
    {
        /** @var User|null $user */
        $user = Auth::user();

        if ($user && !$user->isAccountActive()) {
            $message = 'Your account is deactivated';
            if ($user->isPractice()) {
                return redirect()->route('practice.details.base')->with('error', $message);
            } elseif ($user) {
                return redirect()->route('account-details')->with('error', $message);
            }
        }
        return $next($request);
    }
}
