<?php

declare(strict_types=1);

namespace App\Notifications\Shift;

use App\Entities\Shift\Shift;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Class ShiftCanceledNotification
 * @package App\Notifications\Shift
 */
class ShiftCanceledNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * @var Shift
     */
    private $shift;

    /**
     * AcceptShiftNotification constructor.
     * @param Shift $shift
     */
    public function __construct(Shift $shift)
    {
        $this->shift = $shift;
    }

    /**
     * @param $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * @param $notifiable
     * @return MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage())
            ->subject('Update - Your Upcoming Shift Canceled!')
            ->view('emails.shift.shift-canceled', ['shift' => $this->shift]);
    }
}
