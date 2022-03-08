<?php

namespace App\Notifications\Shift;

use App\Entities\Shift\Shift;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

/**
 * Class AcceptShiftNotification
 * @package App\Notifications\Shift
 */
class AcceptShiftNotification extends Notification implements ShouldQueue
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
        $subject = $this->shift->invite_subject;
        $lunchText = '';
        if ($this->shift->lunch_break > 0) {
            $lunchText = " (including " . $this->shift->lunch_break . "min. lunch break)";
        }
        return (new MailMessage())->subject($subject ?: 'You have been invited to work via Boon!')->view(
            'emails.shift.invited-to-job',
            ['shift' => $this->shift, 'name' => $notifiable->first_name, 'lunchText' => $lunchText]
        );
            /*->subject('You have been invited to job')
            ->greeting('Hello!')
            ->line('You have been invited to job.')
            ->action('submit', route('shifts.provider.acceptPage', ['shift' => $this->shift]))
            ->line('Thank you for using our application!');*/
    }
}
