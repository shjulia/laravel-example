<?php

declare(strict_types=1);

use App\Entities\User\User;
use App\Events\Maps\TimeZoneEvent;
use Illuminate\Database\Seeder;

/**
 * Class TimeZoneSeeder
 */
class TimeZoneSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::where('tz', null)->with('specialist')->getModels();
        foreach ($users as $user) {
            event(new TimeZoneEvent($user));
        }
    }
}
