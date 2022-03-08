<?php

declare(strict_types=1);

namespace App\UseCases\Admin\Notifications;

use App\Entities\User\User;
use App\Mail\Admin\PaymentProblem;
use App\Repositories\User\UserRepository;
use Illuminate\Contracts\Mail\Mailer;
use Mail;

/**
 * Class PaymentsProblemService
 * Notify if there are any problems with payments.
 *
 * @package App\UseCases\Admin\Notifications
 */
class PaymentsProblemService
{
    /**
     * @var UserRepository
     */
    protected $userRepository;

    /**
     * @var Mailer
     */
    private $mailer;

    /**
     * PaymentsProblemService constructor.
     * @param UserRepository $userRepository
     * @param Mailer $mailer
     */
    public function __construct(UserRepository $userRepository, Mailer $mailer)
    {
        $this->userRepository = $userRepository;
        $this->mailer = $mailer;
    }

    /**
     * @param User $user
     */
    public function notify(User $user)
    {
        try {
            $this->mailer->to([
                config('app.ceo_email'),
                config('app.developer_email'),
                config('app.manager_email'),
            ])->send(new PaymentProblem($user));
        } catch (\Exception $e) {
            \LogHelper::error($e);
        }
    }
}
