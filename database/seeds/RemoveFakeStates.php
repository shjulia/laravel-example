<?php

use Illuminate\Database\Seeder;

class RemoveFakeStates extends Seeder
{
    public function run()
    {
        $arr = [
            'AMERICAN SAMOA',
            'DISTRICT OF COLUMBIA',
            'FEDERATED STATES OF MICRONESIA',
            'GUAM GU',
            'MARSHALL ISLANDS',
            'PALAU',
            'PUERTO RICO',
            'VIRGIN ISLANDS',
            'NORTHERN MARIANA ISLANDS'
        ];

        \App\Entities\Data\State::whereIn('title', $arr)->delete();
    }
}