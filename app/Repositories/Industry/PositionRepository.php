<?php

namespace App\Repositories\Industry;

use App\Entities\Industry\Industry;
use App\Entities\Industry\Position;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Class PositionRepository
 * @package App\Repositories\Industry
 */
class PositionRepository
{
    /**
     * @return Position[]
     */
    public function getAll()
    {
        return Position::get();
    }

    /**
     * @return Position[]
     */
    public function getAllWithChildren()
    {
        return Position::whereNull('parent_id')->with('children')->get();
    }

    /**
     * @return Position[]|\Illuminate\Database\Eloquent\Collection
     */
    public function getParents()
    {
        return Position::whereNull('parent_id')->get();
    }

    /**
     * @return Position[]|\Illuminate\Database\Eloquent\Collection
     */
    public function findGrouped()
    {
        return Position::leftJoin('industries', 'positions.industry_id', '=', 'industries.id')
            ->select('positions.*', DB::raw('industries.title as industry'))
            ->orderBy('industry_id')
            ->get()
            ->groupBy('industry');
    }

    public function findGroupedWithChildren()
    {
        return Position::leftJoin('industries', 'positions.industry_id', '=', 'industries.id')
            ->select('positions.*', DB::raw('industries.title as industry'))
            ->orderBy('industry_id')
            ->whereNull('positions.parent_id')
            ->with('children')
            ->get()
            ->groupBy('industry');
    }

    /**
     * @param Industry $industry
     * @return Position
     */
    public function getByIndustry(Industry $industry)
    {
        return Position::where('industry_id', $industry->id)->get();
    }

    /**
     * @param int $id
     * @return Position
     */
    public function getById(int $id): Position
    {
        if (!$position = Position::where('id', $id)->first()) {
            throw new \DomainException('Position not found');
        }
        return $position;
    }

    /**
     * @param string $title
     * @return Position
     */
    public function getByTitle(string $title): Position
    {
        if (!$position = Position::where('title', $title)->first()) {
            throw new \DomainException('Position not found');
        }
        return $position;
    }

    /**
     * @param int $id
     * @return Position|null
     */
    public function findById(int $id): ?Position
    {
        return Position::where('id', $id)->first();
    }

    /**
     * @param Request $request
     * @return Position[]
     */
    public function findByQueryParams(Request $request)
    {
        $query = Position::orderByDesc('id');
        if (!empty($value = $request->get('title'))) {
            $query->where('title', 'like', '%' . $value . '%');
        }
        if (!empty($value = $request->get('industry'))) {
            $query->where('industry_id', $value);
        }
        return $query->with('industry')->paginate(20);
    }
}
