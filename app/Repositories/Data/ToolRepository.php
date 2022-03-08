<?php

declare(strict_types=1);

namespace App\Repositories\Data;

use App\Entities\Data\Tool;
use Illuminate\Database\Eloquent\Collection;

/**
 * Class ToolRepository
 * @package App\Repositories\Data
 */
class ToolRepository
{
    /**
     * @return Tool[]|Collection
     */
    public function findPaginate()
    {
        return Tool::orderBy('id', 'DESC')->paginate();
    }

    /**
     * @return Tool[]|Collection
     */
    public function findAll()
    {
        return Tool::orderBy('id', 'DESC')->get();
    }

    /**
     * @param int $id
     * @return Tool
     */
    public function getById(int $id): Tool
    {
        if (!$tool = Tool::where('id', $id)->first()) {
            throw new \DomainException('Tool not found');
        }
        return $tool;
    }
}
