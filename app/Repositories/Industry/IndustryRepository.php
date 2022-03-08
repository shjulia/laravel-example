<?php

namespace App\Repositories\Industry;

use App\Entities\Industry\Industry;
use Illuminate\Http\Request;

/**
 * Class IndustryRepository
 * @package App\Repositories\Industry
 */
class IndustryRepository
{
    /** @var string */
    public const DEFAULT = 'Dental';

    /**
     * @return Industry[]
     */
    public function getAll()
    {
        return Industry::get();
    }

    /**
     * @param int $id
     * @return Industry
     */
    public function getById(int $id): Industry
    {
        if (!$industry = Industry::where('id', $id)->first()) {
            throw new \DomainException('Industry not found');
        }
        return $industry;
    }

    /**
     * @param string $alias
     * @return int|null
     */
    public function getIDByIndustryAlias(string $alias): ?int
    {
        if (!$industry = Industry::where('alias', $alias)->first()) {
            return null;
        }
        return $industry->id;
    }

    /**
     * @param int|null $id
     * @return Industry|null
     */
    public function getByIndustry(?int $id): ?Industry
    {
        if ($id && ($industry = Industry::where('id', $id)->first())) {
            return $industry;
        }
        return null;
    }

    /**
     * @return Industry|null
     */
    public function getDentalIndustry(): ?Industry
    {
        if ($industry = Industry::where('alias', 'dental')->first()) {
            return $industry;
        }
        return null;
    }

    /**
     * @param Request $request
     * @return Industry[]
     */
    public function findByQueryParams(Request $request)
    {
        $query = Industry::orderByDesc('id');
        if (!empty($value = $request->get('industry'))) {
            $query->where('title', 'like', '%' . $value . '%');
        }
        return $query->paginate(20);
    }
}
