<?php

declare(strict_types=1);

namespace App\Mail\Shift;

use App\Entities\Shift\Shift;
use App\Entities\User\User;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ShiftFinished extends Mailable
{
    use SerializesModels;

    /** @var User $provider */
    public $provider;

    /**
     * @var Shift $shift
     */
    public $shift;

    /**
     * ShiftFinished constructor.
     * @param User $provider
     * @param Shift $shift
     */
    public function __construct(User $provider, Shift $shift)
    {
        $this->provider = $provider;
        $this->shift = $shift;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.shift.shift-finished')->text('emails.shift.text.shift-finished-text');
    }
}
