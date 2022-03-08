<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(IndustrySeeder::class);
        $this->call(DentalAssistantSeeder::class);
        $this->call(USAStatesSeeder::class);
        $this->call(RolesSeeder::class);
        $this->call(CustomerSuccessSeeder::class);
        $this->call(DentalSpecialitiesSeeder::class);
        $this->call(TierSeeder::class);
        $this->call(ToolsSeeder::class);
        $this->call(LicenceTypeSeeder::class);
        $this->call(ScoreSeeder::class);
    }
}
