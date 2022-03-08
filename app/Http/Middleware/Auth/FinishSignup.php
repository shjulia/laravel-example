<?php

namespace App\Http\Middleware\Auth;

use App\Entities\User\User;
use Closure;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

/**
 * Class FinishSignup
 * @package App\Http\Middleware\Auth
 */
class FinishSignup
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        /** @var User|null $user */
        if ($user = Auth::user()) {
            if ($user->tmp_token && $user->signup_step) {
                $signupStep = explode(':', $user->signup_step);
                if (!isset($signupStep[0]) || !isset($signupStep[1])) {
                    return $next($request);
                }
                $tmpCode = $user->tmp_token;
                $urlBase = '';
                if ($signupStep[0] == 'provider') {
                    Auth::logout();
                    return redirect()->route('need', ['code' => $user->tmp_token]); // this is new task
                    //$urlBase = 'signup.';
                } elseif ($signupStep[0] == 'practice') {
                    $urlBase = 'practice.signup.';
                } elseif ($signupStep[0] == 'base') {
                    $urlBase = 'base.signup.';
                }
                if ($request->isApi) {
                    return response()->json(
                        ['error' => 'You must complete signup process in step ' . $signupStep[1]],
                        Response::HTTP_BAD_GATEWAY
                    );
                }
                Auth::logout();
                return redirect(route($urlBase ? $urlBase . $signupStep[1] : '', ['code' => $tmpCode]));
            }
        }
        return $next($request);
    }
}
