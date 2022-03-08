<?php

namespace App\Http\Middleware;

use App\Entities\User\User;
use Closure;
use Illuminate\Support\Facades\Auth;

/**
 * Class Allow
 * @package App\Http\Middleware
 */
class Allow
{
    /**
     * @param $request
     * @param Closure $next
     * @return \Illuminate\Http\RedirectResponse|mixed
     */
    public function handle($request, Closure $next)
    {
        /** @var User|null $user */
        /*$user = Auth::user();
        if (!$user->isPractice() && !$user->isProvider()) {
            return $next($request);
        }
        $notAllowRouteName = 'dashboard';
        if (!env('APP_ALLOW')) {
            return redirect()->route($notAllowRouteName);
        }
        if ($user->isPractice() && !($user->practice->area_id && $user->practice->area->isOpen())) {
            return redirect()->route($notAllowRouteName);
        }
        if ($user->isProvider() && !($user->specialist->area_id && $user->specialist->area->isOpen())) {
            return redirect()->route($notAllowRouteName);
        }*/
        return $next($request);
    }
}
