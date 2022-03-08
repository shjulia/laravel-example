<?php

declare(strict_types=1);

namespace App\UseCases\Emails\Provider;

use App\Entities\User\User;
use App\Mail\Signup\FinishSignup;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;

/**
 * Class SignupReminderService
 * Reminds user to finish sign up base on time intervals.
 *
 * @package App\UseCases\Emails\Provider
 */
class SignupReminderService
{
    /**
     * time for signup in minutes
     *
     * @var int
     */
    protected $timeForSignup = 30;

    /**
     * remind range
     *
     * @var int
     */
    protected $range = 30;

    /**
     * signup reminder intervals (in hours)
     *
     * @var array
     */
    //protected $intervals = [24,72,168,240,504,1080];
    protected $intervals = [24,48,96,72,264,576];

    /**
     * @param User $user
     * @return bool
     */
    public function remind(User $user): bool
    {
        $signupTime = Carbon::parse($user->created_at)->addMinutes($this->timeForSignup);

        if ($signupTime > now()) {
            return false;
        }

        return $this->sendEmail($user, $user->signup_reminder_counter);
    }

    /**
     * send finish signup reminder email
     *
     * @param $user
     * @param $interval_index
     * @return bool
     */
    private function sendEmail($user, $interval_index): bool
    {
        $timeFrom = $user->last_remind_action_time ?: $user->updated_at;
        $intervalTime = $timeFrom->addHours($this->intervals[$interval_index]);

        if ($intervalTime < now()) {
            Mail::to($user)->send(new FinishSignup($user));
            return true;
        }

        return false;
    }
}
