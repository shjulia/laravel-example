<?php

use App\Entities\Data\Location\ZipCode;
use App\Entities\Data\State;
use Illuminate\Database\Seeder;

class CitySeeder extends Seeder
{
    public function run()
    {
        echo 'Filling cities_init table...' . PHP_EOL;
        $citiesPath = base_path('storage') . '/geo/cities.sql';
        DB::unprepared(file_get_contents($citiesPath));

        echo 'Filling zip_init table...' . PHP_EOL;
        $cities_extended = base_path('storage') . '/geo/cities_extended.sql';
        DB::unprepared(file_get_contents($cities_extended));

        DB::statement('SET FOREIGN_KEY_CHECKS = 0');
        DB::table('area_places')->truncate();
        DB::table('areas')->truncate();

        echo 'Filling zip_codes table...' . PHP_EOL;
        DB::table('zip_codes')->truncate();
        $zips = DB::table('zip_init')->get();
        foreach ($zips as $zip) {
            DB::table('zip_codes')->insert([
                'zip' => $zip->zip,
                'place_name' => $zip->city,
                'state_code' => $zip->state_code,
                'lat' => $zip->latitude,
                'lng' => $zip->longitude,
                'county' => $zip->county,
            ]);
        }

        echo 'Filling cities table...' . PHP_EOL;
        DB::table('cities')->truncate();
        $cities = DB::table('cities_init')->get();
        foreach ($cities as $city) {
            $state = State::where('short_title', $city->state_code)->first();
            if (!isset($state)) {
                continue;
            }
            $zip = ZipCode::where([
                'place_name' => $city->city,
                'state_code' => $city->state_code
            ])->first();
            DB::table('cities')->insert([
                'name' => $city->city,
                'state' => $state->id,
                'lat' => $zip->lat,
                'lng' => $zip->lng,
            ]);
        }

        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
}
