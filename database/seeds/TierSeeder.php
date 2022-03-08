<?php

use App\Entities\Data\Tier;
use Illuminate\Database\Seeder;

class TierSeeder extends Seeder
{
    public function run()
    {
        $tiers = [
            [
                'value' => 1,
                'multiplier' => 1.25
            ],
            [
                'value' => 2,
                'multiplier' => 1.15
            ],
            [
                'value' => 3,
                'multiplier' => 1
            ],
            [
                'value' => 4,
                'multiplier' => 0.9
            ],
        ];
        foreach ($tiers as $tier) {
            Tier::create($tier);
        }
    }
}