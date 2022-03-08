<?php

use App\Entities\Data\LicenseType;
use App\Entities\Data\LicenseTypePosition;
use App\Entities\Data\State;
use Illuminate\Database\Seeder;

class LicenceTypeSeeder extends Seeder
{
    public function run()
    {
        foreach (["Test license", "Test license 2", "Test license 3"] as $title) {
            $licenseType = LicenseType::create([
                'title' => $title
            ]);

            /** @var LicenseTypePosition $licenseTypePositions */
            $licenseTypePositions = $licenseType->licenseTypePositions()->create([
                'position_id' => 2,
                'required' => 1
            ]);
            $licenseTypePositions->states()->attach(State::get()->only('id')->toArray());
        }
    }
}
