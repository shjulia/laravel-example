<?php

declare(strict_types=1);

namespace App\Console\Commands\Users;

use App\Entities\User\Role;
use App\Entities\User\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

/**
 * Class MakeAccountant
 * Assigns user to role Accountant by email.
 *
 * @package App\Console\Commands\Users
 */
class MakeAccountant extends Command
{
    /**
     * @var string
     */
    protected $signature = 'user:accountant {email} {--assign}';

    /**
     * @var string
     */
    protected $description = 'Assign user to role Accountant by email';

    public function handle()
    {
        $role = Role::where('type', Role::ROLE_ACCOUNTANT)->first();

        if (!$this->option('assign')) {
            /** @var User $user */
            $user = User::make([
                'email' => $this->argument('email'),
                'first_name' => 'Accountant',
                'last_name' => 'Accountant',
                'phone' => '+111111111111',
                'password' => Hash::make('secret'),
            ]);
            $user->setActiveStatus();
            $user->saveOrFail();
            $user->roles()->attach($role->id);
            $this->successMessage();
        } else {
            $user = User::where('email', $this->argument('email'))->first();

            if (!$user) {
                $this->info('User with such email was not found.');
                return;
            }
            $user->roles()->detach();
            $user->roles()->attach($role);
            $this->successMessage();
        }
    }

    public function successMessage()
    {
        $this->info('User was assigned to accountant role');
    }
}
