<?php

use App\Entities\User\Practice\Practice;
use App\Entities\User\Provider\Specialist;
use App\Entities\User\User;
use Illuminate\Database\Seeder;

/**
 * Class UserStatusToTypesStatus
 */
class UserStatusToTypesStatus extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        /** @var User[] $users */
        $users = User::get();
        foreach ($users as $user) {
            if ($user->isPractice() && $practice = $user->practice) {
                if ($practice->pivot->is_creator) {
                    $practice->update([
                        'approval_status' => $user->isActive() ? Practice::STATUS_APPROVED : Practice::STATUS_WAITING
                    ]);
                }
            }
            if ($user->isProvider() && $provider = $user->specialist) {
                $provider->update([
                    'approval_status' => $user->isActive() ? Specialist::STATUS_APPROVED : Specialist::STATUS_WAITING
                ]);
            }
        }
    }
}
