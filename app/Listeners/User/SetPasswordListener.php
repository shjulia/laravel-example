<?php

namespace App\Listeners\User;

use App\Entities\User\SetPassword;
use App\Events\User\SetPasswordEvent;
use App\Mail\Signup\WelcomeMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

/**
 * Class SetPasswordListener
 * Creates random password for user during registration. Sends email to remind set a new password.
 *
 * Events {@see \App\Events\User\SetPasswordEvent}
 * @package App\Listeners\User
 */
class SetPasswordListener implements ShouldQueue
{
    /**
     * @param SetPasswordEvent $event
     * @throws \Exception
     */
    public function handle(SetPasswordEvent $event): void
    {
        $user = $event->user;
        SetPassword::where('email', $user->email)->delete();
        $token = str_random(10);
        /** @var SetPassword $setPassword */
        $setPassword = SetPassword::make(['email' => $user->email]);
        $setPassword->setToken($token);
        $setPassword->save();
        $this->sentSetPasswordEmail($user, $token);
    }

    /**
     * sent set password email
     *
     * @param $user
     * @param $token
     */
    public function sentSetPasswordEmail($user, $token)
    {
        $setPasswordLink = route('set-password-form', ['token' => $token]);
        Mail::to($user)->send(new WelcomeMail($setPasswordLink));
    }
}
