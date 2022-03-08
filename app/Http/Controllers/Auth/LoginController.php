<?php

namespace App\Http\Controllers\Auth;

use App\Entities\User\User;
use App\Events\User\LoginEvent;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */
    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * @param Request $request
     * @param User $user
     */
    protected function authenticated(Request $request, $user)
    {
        event(new LoginEvent($user));

        if ($user->isRejected()) {
            Auth::logout();
            abort(
                403,
                'Thank you for your interest in Boon. At this time, we are unable to approve your user account.'
            );
        }
        if ($user->isProvider()) {
            $this->redirectTo = '/shifts/provider';
        } elseif ($user->isPractice()) {
            $this->redirectTo = '/shifts';
        }
    }
}
