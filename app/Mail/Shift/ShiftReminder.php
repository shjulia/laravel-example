<?php

declare(strict_types=1);

namespace App\Mail\Shift;

use App\Entities\Shift\Shift;
use App\Entities\User\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ShiftReminder extends Mailable implements ShouldQueue
{
    use Queueable;
    use SerializesModels;

    public $timeLeft;
    /**
     * @var User
     */
    private $user;
    /**
     * @var Shift
     */
    private $shift;

    public function __construct(User $user, Shift $shift, int $timeLeft)
    {
        $this->timeLeft = $timeLeft;
        $this->user = $user;
        $this->shift = $shift;
    }

    /**
     * @return ShiftReminder
     */
    public function build()
    {
        $subject = $this->timeLeft . ' hours left before Shift!';
        return $this->subject($subject)
            ->view('emails.shift.reminder')
            ->text('emails.shift.text.reminder')
            ->with([
                'timeLeft' => $this->timeLeft,
                'userName' => $this->user->full_name,
                'shift' => $this->shift
            ]);
    }
}
