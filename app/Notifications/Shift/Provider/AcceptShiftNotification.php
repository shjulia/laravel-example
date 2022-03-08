<?php

namespace App\Notifications\Shift\Provider;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class AcceptShiftNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * @var int
     */
    private $shiftId;

    /**
     * @var string
     */
    private $providerName;

    /**
     * AcceptShiftNotification constructor.
     * @param int $shiftId
     * @param string $providerName
     */
    public function __construct(int $shiftId, string $providerName)
    {
        $this->shiftId = $shiftId;
        $this->providerName = $providerName;
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
            ->subject('You\'ve been matched')
            ->view('emails.shift.matched', [
                'providerName' => $this->providerName,
                'shiftId' => $this->shiftId
            ]);
        /*return (new MailMessage)
            ->subject('You\'ve been matched')
            ->greeting('Congratulations!')
            ->line($this->providerName . " is on the way!")
            ->action('shift details', route('shifts.details', ['shift' => $this->shiftId]))
            ->line('Thank you for using our application!');*/
    }
}
