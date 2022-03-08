<?php

declare(strict_types=1);

namespace App\Console\Commands\Users;

use App\Entities\User\Role;
use App\Entities\User\User;
use Hash;
use Illuminate\Console\Command;

/**
 * Class MakeUserCustomerSuccess
 * Assigns user to role Customer Success by email.
 *
 * @package App\Console\Commands\Users
 */
class MakeUserCustomerSuccess extends Command
{
    /**
     * @var string
     */
    protected $signature = 'user:customer-success {email} {--assign}';

    /**
     * @var string
     */
    protected $description = 'Assign user to role Customer Success by email';

    public function handle()
    {
        $role = Role::where('type', Role::ROLE_CUSTOMER_SUCCESS)->first();

        if (!$this->option('assign')) {
            /** @var User $user */
            $user = User::make([
                'email' => $this->argument('email'),
                'first_name' => 'Customer',
                'last_name' => 'Success',
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
        $this->info('User was assigned to Customer Success role');
    }
}
