<?php

use Illuminate\Database\Seeder;

/**
 * Class ScoreSeeder
 */
class ScoreSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('scores')->insert([
            ['title' => 'Patient Care', 'for_type' => 'provider', 'active' => 1],
            ['title' => 'Friendly', 'for_type' => 'provider', 'active' => 1],
            ['title' => 'Hard Worker', 'for_type' => 'provider', 'active' => 1],
            ['title' => 'Works well with team', 'for_type' => 'provider', 'active' => 1],
            ['title' => 'Friendly team', 'for_type' => 'practice', 'active' => 1],
            ['title' => 'Cool Office', 'for_type' => 'practice', 'active' => 1],
            ['title' => 'Great Patient', 'for_type' => 'practice', 'active' => 1],
            ['title' => 'Well Organized', 'for_type' => 'practice', 'active' => 1],
        ]);
    }
}