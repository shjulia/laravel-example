<?php

use App\Entities\User\Practice\Practice;
use App\Entities\User\Provider\Specialist;
use App\Events\Maps\Geocoder\PracticeGeocodeEvent;
use App\Events\Maps\Geocoder\SpecialistGeocodeEvent;
use Illuminate\Database\Seeder;

/**
 * Class GeocodeSeeder
 */
class GeocodeSeeder extends Seeder
{
    public function run(): void
    {
        /** @var Specialist[] $specialists */
        $specialists = Specialist::where(['lat' => null, 'lng' => null])
            ->where('driver_address', '!=', null)->get();
        foreach ($specialists as $specialist) {
            event(new SpecialistGeocodeEvent($specialist));
        }

        /** @var Practice[] $practices */
        $practices = Practice::where(['lat' => null, 'lng' => null])
            ->where('address', '!=', null)
            ->get();
        foreach ($practices as $practice) {
            event(new PracticeGeocodeEvent($practice));
        }
    }
}
