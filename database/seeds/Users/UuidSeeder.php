<?php

declare(strict_types=1);

use App\Entities\User\User;
use Illuminate\Database\Seeder;

/**
 * Class UuidSeeder
 */
class UuidSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::where('uuid', null)->getModels();
        foreach ($users as $user) {
            if ($user->uuid) {
                continue;
            }
            $user->update(['uuid' => \Ramsey\Uuid\Uuid::uuid4()]);
        }
    }
}
