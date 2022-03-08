<?php

namespace App\Repositories\Industry;

use App\Entities\Industry\Task;

/**
 * Class TaskRepository
 * @package App\Repositories\Industry
 */
class TaskRepository
{
    /**
     * @return Task[]
     */
    public function findAll()
    {
        return Task::get();
    }

    /**
     * @return Task[]
     */
    public function findByQuery()
    {
        return Task::orderBy('id', 'DESC')->paginate();
    }

    /**
     * @param int $positionId
     * @return Task[]
     */
    public function findAllByPosition(int $positionId)
    {
        return Task::where('position_id', $positionId)->get();
    }

    /**
     * @param int $id
     * @return Task
     */
    public function getById(int $id): Task
    {
        if (!$task = Task::where('id', $id)->first()) {
            throw new \DomainException('Task not found');
        }

        return $task;
    }

    /**
     * @param $position_id
     * @return Task[]|\Illuminate\Database\Eloquent\Collection
     */
    public function getByPosition($position_id)
    {
        return Task::where('position_id', $position_id)->get();
    }
}
