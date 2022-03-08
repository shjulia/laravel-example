<?php

namespace App\Http\Controllers\Auth;

use App\Entities\User\User;
use App\Http\Controllers\Controller;
use App\UseCases\Auth\PasswordSetupService;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ResetPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */
    use ResetsPasswords;

    /**
     * Where to redirect users after resetting their password.
     *
     * @var string
     */
    protected $redirectTo = '/home';
    /**
     * @var PasswordSetupService
     */
    private $passwordSetupService;

    /**
     * Create a new controller instance.
     *
     * @param PasswordSetupService $passwordSetupService
     */
    public function __construct(PasswordSetupService $passwordSetupService)
    {
        //$this->middleware('guest');
        $this->passwordSetupService = $passwordSetupService;
    }

    /**
     * Reset the given user's password.
     *
     * @param  \Illuminate\Contracts\Auth\CanResetPassword  $user
     * @param  string  $password
     * @return void
     */
    protected function resetPassword(User $user, $password)
    {
        $user->password = Hash::make($password);
        $user->setRememberToken(Str::random(60));
        $user->save();
        $this->passwordSetupService->saveSetup($user);
        event(new PasswordReset($user));
        $this->guard()->login($user);
    }

    protected function rules()
    {
        return [
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|confirmed|min:12|max:50|password',
        ];
    }
}
