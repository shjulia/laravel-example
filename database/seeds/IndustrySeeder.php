<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Class IndustrySeeder
 */
class IndustrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('industries')->insert([
            ['title' => 'Dental', 'alias' => 'dental'],
            ['title' => 'Veterinarian', 'alias' => 'veterinarian'],
            ['title' => 'Medical', 'alias' => 'medical']
        ]);

        $id = DB::table('industries')->where('title', 'Dental')->first()->id;

        DB::table('positions')->insert([
            ['industry_id' => $id, 'title' => 'Dental Assistant', 'fee' => 10, 'minimum_profit' => 8],
            ['industry_id' => $id, 'title' => 'Dentists', 'fee' => 10, 'minimum_profit' => 8],
            ['industry_id' => $id, 'title' => 'Dental Hygienist', 'fee' => 10, 'minimum_profit' => 8]
        ]);
    }
}
