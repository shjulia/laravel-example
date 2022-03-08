<?php

use App\Entities\User\Provider\Specialist;
use Illuminate\Database\Seeder;

/**
 * Class DefaultDistanceSeeder
 */
class DefaultDistanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Specialist::where('shift_distance_max', null)->update([
            'shift_distance_max' => 25
        ]);
    }
}
