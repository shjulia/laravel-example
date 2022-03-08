<?php

namespace App\Listeners\User;

use App\Events\User\PartnerRegisterEvent;
use App\Mail\Signup\PartnerWelcomeMail;
use Illuminate\Support\Facades\Mail;

/**
 * Class PartnerRegisterListener
 * Sends Welcome Mail to Partner after registration.
 *
 * Event {@see \App\Events\User\PartnerRegisterEvent}
 * @package App\Listeners\User
 */
class PartnerRegisterListener
{
    /**
     * @param PartnerRegisterEvent $event
     */
    public function handle(PartnerRegisterEvent $event): void
    {
        $user = $event->user;
        $this->sentWelcomeEmail($user);
    }

    /**
     * sent welcome email
     *
     * @param $user
     */
    public function sentWelcomeEmail($user)
    {
        Mail::to($user->email)->send(new PartnerWelcomeMail($user));
    }
}
