<?php

namespace App\Notifications\User;

use App\Entities\User\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

/**
 * @deprecated
 * Class AccountCreatedNotification
 * @package App\Notifications\User
 */
class AccountCreatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * @var string
     */
    private $route;

    /**
     * AccountCreatedNotification constructor.
     * @param string $route
     */
    public function __construct(string $route)
    {
        $this->route = $route;
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
            ->line(' Please take a moment to finish sign-up process here.')
            ->action('finish sign-up process', $this->route)
            ->line(
                'If you have any questions, please feel free to reach out. We are here to help and together ' .
                'make positive impact.'
            )
            ->level('success')
            ->salutation(' ');
    }
}
