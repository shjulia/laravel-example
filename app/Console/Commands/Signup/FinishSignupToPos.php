<?php

declare(strict_types=1);

namespace App\Console\Commands\Signup;

use App\Entities\User\User;
use App\Mail\Signup\FinishSignup;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

/**
 * Class FinishSignupToPos
 * Sends an email to providers who have not completed registration by position.
 *
 * @package App\Console\Commands\Signup
 */
class FinishSignupToPos extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'signup:finish_remind {position} {subject}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send an email to providers who have not completed registration by position';

    public function handle()
    {
        $position = $this->argument('position');
        $subject = $this->argument('subject');
        $users = User::whereHas('specialist', function ($query) use ($position) {
            $query->where('position_id', $position);
        })
            ->where('signup_step', '!=', null)
            ->where('tmp_token', '!=', null)
            ->where('signup_reminder_counter', '!=', 6)
            ->get();

        foreach ($users as $user) {
            Mail::to($user)->send(new FinishSignup($user, $subject));
        }
    }
}
