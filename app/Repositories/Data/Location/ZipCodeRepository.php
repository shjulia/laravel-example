<?php

namespace App\Repositories\Data\Location;

use App\Entities\Data\Location\ZipCode;
use App\Entities\Data\State;

/**
 * Class ZipCodeRepository
 * @package App\Repositories\Data\Location
 */
class ZipCodeRepository
{
    /**
     * @param State $state
     * @return ZipCode[]|\Illuminate\Database\Eloquent\Collection
     */
    public function getByState(State $state)
    {
        return ZipCode::where('state_code', $state->short_title)->get();
    }

    /**
     * @param string $zip
     * @return ZipCode|\Illuminate\Database\Eloquent\Model|object|null
     */
    public function getByZip(string $zip)
    {
        return ZipCode::where('zip', $zip)->first();
    }
}
