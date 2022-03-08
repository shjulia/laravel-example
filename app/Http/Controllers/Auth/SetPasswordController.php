<?php

namespace App\Http\Controllers\Auth;

use App\Entities\User\SetPassword;
use App\Entities\User\User;
use App\Helpers\EncryptHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\AdditionalRequest;
use Illuminate\Support\Facades\Auth;

/**
 * Class SetPasswordController
 * @package App\Http\Controllers\Auth
 */
class SetPasswordController extends Controller
{
    /**
     * @param string $token
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showForm(string $token)
    {
        $this->findByToken($token);
        return view('auth.passwords.set-password', compact('token'));
    }

    /**
     * @param AdditionalRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function save(AdditionalRequest $request)
    {
        try {
            $setPassword = $this->findByToken($request->token);
            $user = User::where('email', $setPassword->email)->first();
            $user->password = bcrypt($request->password);
            $user->phone = $request->phone;
            $user->save();
            $setPassword->delete();
            if (Auth::guest()) {
                Auth::login($user);
            }
        } catch (\Exception $e) {
            \LogHelper::error($e, ['userId' => $user ? $user->id : null]);
            return back()->with(['error' => 'Saving password error']);
        }

        return redirect()->route('home');
    }

    /**
     * @param string $token
     * @return SetPassword
     */
    private function findByToken(string $token): SetPassword
    {
        $token = md5($token);
        $setPassword = SetPassword::where('token', $token)->first();
        if (!$setPassword) {
            abort('404', 'Token is invalid.');
        }
        return $setPassword;
    }
}
