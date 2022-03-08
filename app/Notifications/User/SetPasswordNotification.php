<?php

namespace App\Notifications\User;

use App\Entities\User\User;
use App\Mail\Signup\WelcomeMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Mail;

/**
 * @deprecated
 * Class SetPasswordNotification
 * @package App\Notifications\User
 */
class SetPasswordNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * @var string
     */
    private $route;

    /**
     * SetPasswordNotification constructor.
     * @param string $token
     */
    public function __construct(string $token)
    {
        $this->route = route('set-password-form', ['token' => $token]);
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
     * @param User $notifiable
     * @return MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage())
            ->subject('Congrats! You\'ve Joined boon!')
            ->greeting('Hello, ' . $notifiable->first_name . ' !')
            ->line('Welcome to boon! We are excited that you are here. Together, we can continue "practicing good".')
            ->line('Please take a moment to set up your password here.')
            ->action('set your password', $this->route)
            ->line(
                'If you have any questions, please feel free to reach out. We are here to help and together ' .
                'make positive impact.'
            )
            ->level('success')
            ->salutation(' ');
    }
}
