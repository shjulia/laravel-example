<?php

declare(strict_types=1);

use App\Entities\User\Role;
use App\Entities\User\User;
use Illuminate\Database\Seeder;

class CustomerSuccessSeeder extends Seeder
{
    /**
     * @throws Throwable
     */
    public function run()
    {
        $role = Role::where('type', Role::ROLE_CUSTOMER_SUCCESS)->first();

        /** @var User $user */
        $user = User::make([
            'email' => 'customer_success@gmail.com',
            'first_name' => 'Customer',
            'last_name' => 'Success',
            'phone' => '+111111111111',
            'password' => Hash::make('secret'),
            'is_test_account' => true
        ]);
        $user->setActiveStatus();
        $user->saveOrFail();
        $user->roles()->attach($role->id);
    }
}