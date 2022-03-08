<?php

use App\Entities\User\User;
use Illuminate\Database\Seeder;

class UserTimesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = User::get();
        foreach ($users as $user) {
            $user->last_signup_action_date = $user->updated_at->format('Y-m-d');
            $user->last_remind_action_time = $user->updated_at;
            $user->save();
        }
    }
}
