<?php

namespace App\Mail\Admin;

use App\Entities\User\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PaymentProblem extends Mailable
{
    use Queueable;
    use SerializesModels;

    /**
     * @var User
     */
    private $user;

    /**
     * PaymentProblem constructor.
     *
     * @param $provider
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $user = $this->user;
        $message = '<h2 style="color:red">
            Provider ' . $user->first_name . ' ' . $user->last_name . ' (id:' . $user->id . ')
            has problems with withdraw of funds
        </h2>';

        return $this->html($message);
    }
}
