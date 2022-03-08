<?php

use App\Entities\User\Practice\Practice;
use App\Entities\User\Provider\Specialist;
use App\Helpers\EncryptHelper;
use Illuminate\Database\Seeder;

/**
 * Class EncryptSeeder
 */
class EncryptSeeder extends Seeder
{
    public function run(): void
    {
        /** @var Specialist[] $specialists */
        $specialists = Specialist::get();
        foreach ($specialists as $specialist) {
            if ($specialist->ssn && strlen($specialist->ssn) < 15) {
                $specialist->update([
                   'ssn' => EncryptHelper::encrypt($specialist->ssn)
                ]);
            }
        }

        /** @var Practice[] $practices */
        $practices = Practice::get();
        foreach ($practices as $practice) {
            if ($practice->stripe_client_id && strlen($practice->stripe_client_id) < 30) {
                $practice->update([
                    'stripe_client_id' => EncryptHelper::encrypt($practice->stripe_client_id)
                ]);
            }
        }
    }
}
