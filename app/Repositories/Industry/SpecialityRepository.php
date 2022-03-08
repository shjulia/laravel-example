<?php

namespace App\Repositories\Industry;

use App\Entities\Industry\Industry;
use App\Entities\Industry\Speciality;
use App\Entities\User\User;
use Illuminate\Http\Request;

/**
 * Class SpecialityRepository
 * @package App\Repositories\Industry
 */
class SpecialityRepository
{
    /**
     * @param User $user
     * @return Speciality[]
     */
    public function findAllByUser(User $user)
    {
        $industry = $user->specialist->industry;
        if (!$industry) {
            throw new \DomainException('Industry not found');
        }
        return $industry->specialities;
    }

    /**
     * @param Industry $industry
     * @return Speciality[]
     */
    public function findByIndustry(Industry $industry)
    {
        return Speciality::where('industry_id', $industry->id)->get();
    }

    /**
     * @param int $id
     * @return Speciality
     */
    public function getById(int $id): Speciality
    {
        if (!$speciality = Speciality::where('id', $id)->first()) {
            throw new \DomainException('Speciality not found');
        }
        return $speciality;
    }

    /**
     * @param Request $request
     * @return Speciality[]
     */
    public function findByQueryParams(Request $request)
    {
        $query = Speciality::orderByDesc('id');
        if (!empty($value = $request->get('title'))) {
            $query->where('title', 'like', '%' . $value . '%');
        }
        if (!empty($value = $request->get('industry'))) {
            $query->where('industry_id', $value);
        }
        return $query->with('industry')->paginate(20);
    }
}
