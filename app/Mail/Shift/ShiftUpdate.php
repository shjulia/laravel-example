<?php

declare(strict_types=1);

namespace App\Mail\Shift;

use App\Entities\Shift\Shift;
use App\Entities\User\User;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

/**
 * Class ShiftUpdate
 * @package App\Mail\Shift
 */
class ShiftUpdate extends Mailable
{
    use SerializesModels;

    /**
     * @var Shift
     */
    private $shift;

    /**
     * @var string
     */
    private $action;

    /**
     * @var User|null
     */
    private $user;

    /**
     * ShiftUpdate constructor.
     * @param Shift $shift
     * @param string $action
     * @param User|null $user
     */
    public function __construct(Shift $shift, string $action, ?User $user)
    {
        $this->shift = $shift;
        $this->action = $action;
        $this->user = $user;
    }

    /**
     * @return ShiftUpdate
     */
    public function build()
    {
        return $this->subject('Update - Shift ' . $this->shift->id)
            ->view('emails.shift.shift-update')
            ->with([
                'shift' => $this->shift,
                'action' => $this->action,
                'userName' => $this->user ? $this->user->full_name : null
            ]);
    }
}
