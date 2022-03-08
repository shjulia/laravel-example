<?php

use App\Entities\User\Role;
use App\Entities\User\User;
use Illuminate\Database\Seeder;

/**
 * Class RolesSeeder
 */
class RolesSeeder extends Seeder
{
    /**
     * @throws Throwable
     */
    public function run()
    {
        $roles = [
            ['title' => 'Super Admin', 'type' => 'super_admin'],
            ['title' => 'Admin', 'type' => 'admin'],
            ['title' => 'Provider', 'type' => 'provider'],
            ['title' => 'Practice', 'type' => 'practice'],
            ['title' => 'Partner', 'type' => 'partner'],
            ['title' => 'Customer Success', 'type' => 'customer_success'],
            ['title' => 'Accountant', 'type' => 'accountant'],
        ];

        foreach ($roles as $role) {
            if (!Role::where('type', $role['type'])->first()) {
                DB::table('roles')->insert($role);

                if ($role['type'] == Role::ROLE_SUPER_ADMIN) {
                    $this->createDefaultSuperAdmin();
                }
            }
        }
    }

    /**
     * @throws Throwable
     */
    private function createDefaultSuperAdmin()
    {
        $role = Role::where('title', 'Super Admin')->first();

        /** @var User $user */
        $user = User::make([
            'email' => 'admin@gmail.com',
            'first_name' => 'Admin',
            'last_name' => 'Admin',
            'phone' => '+111111111111',
            'password' => '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm' //secret
        ]);
        $user->setActiveStatus();
        $user->saveOrFail();
        $user->roles()->attach($role->id);
    }
}