<?php

use Illuminate\Database\Seeder;

class CreateCheckrSeeder extends Seeder
{
    public function run()
    {
        $specialists = \App\Entities\User\Provider\Specialist::with('checkr')->get();
        foreach ($specialists as $specialist) {
            if (!$specialist->checkr) {
                $specialist->checkr()->create();
            }
        }
    }
}