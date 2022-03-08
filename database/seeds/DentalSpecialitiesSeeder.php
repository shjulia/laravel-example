<?php

use Illuminate\Database\Seeder;

/**
 * Class DentalSpecialitiesSeeder
 */
class DentalSpecialitiesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $id = DB::table('industries')->where('title', 'Dental')->first()->id;

        DB::table('specialities')->insert([
            ['industry_id' => $id, 'title' => 'Endodontist'],
            ['industry_id' => $id, 'title' => 'Orthodontist'],
            ['industry_id' => $id, 'title' => 'Prosthodontist']
        ]);
    }
}
