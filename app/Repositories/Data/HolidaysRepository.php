<?php

namespace App\Repositories\Data;

use App\Entities\Data\Holiday;

/**
 * Class HolidaysRepository
 * @package App\Repositories\Data
 */
class HolidaysRepository
{
    /**
     * @return Holiday[]
     */
    public function getAll()
    {
        return Holiday::getModels();
    }
}
