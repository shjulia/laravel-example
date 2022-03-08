<?php

declare(strict_types=1);

namespace App\Repositories\Data;

use App\Entities\Data\Privacy;

class PrivacyRepository
{
    /**
     * @return Privacy[]
     */
    public function findAll()
    {
        return Privacy::orderBy('id', 'DESC')->with('admin')->get();
    }

    /**
     * @return Privacy
     */
    public function getLast(): Privacy
    {
        $privacy = Privacy::orderBy('id', 'DESC')->first();
        if (!$privacy) {
            throw new \DomainException('Privacy not found');
        }
        return $privacy;
    }
}
