<?php

declare(strict_types=1);

namespace App\Console\Commands\Signup;

use App\Entities\User\User;
use App\UseCases\Emails\Provider\SignupReminderService;
use Carbon\Carbon;
use Illuminate\Console\Command;

/**
 * Class FinishSignupEmails
 * Sends an email to providers who have not completed registration to remind them finish sign up.
 *
 * @package App\Console\Commands\Signup
 */
class FinishSignupEmails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'signup:emails';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send an email to providers who have not completed registration';

    /**
     * Signup Reminder Service
     *
     * @var object
     */
    protected $signupReminderService;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->signupReminderService = new SignupReminderService();
        parent::__construct();
    }

    public function handle()
    {
        $users = User::whereHas('specialist')
            ->where('signup_step', '!=', null)
            ->where('tmp_token', '!=', null)
            ->where('signup_reminder_counter', '!=', 6)
            ->get();

        foreach ($users as $user) {
            try {
                if ($this->signupReminderService->remind($user)) {
                    //echo $user->id . PHP_EOL;
                    $user->update([
                        'signup_reminder_counter' => $user->signup_reminder_counter + 1,
                        'last_remind_action_time' => Carbon::now()
                    ]);
                }
                sleep(1);
            } catch (\Exception $e) {
                \LogHelper::error($e);
            }
        }
    }
}
