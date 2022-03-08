<?php

declare(strict_types=1);

namespace App\UseCases\Admin\Manage\Data\Positions;

use App\Entities\Industry\Task;
use App\Http\Requests\Admin\Data\Task\CreateRequest;
use App\Http\Requests\Admin\Data\Task\EditRequest;
use App\Repositories\Industry\TaskRepository;

/**
 * Class TaskService
 * Manage tasks.
 *
 * @package App\UseCases\Admin\Manage\Data\Positions
 */
class TaskService
{
    /**
     * @var TaskRepository
     */
    private $taskRepository;

    /**
     * TaskService constructor.
     * @param TaskRepository $taskRepository
     */
    public function __construct(TaskRepository $taskRepository)
    {
        $this->taskRepository = $taskRepository;
    }

    /**
     * @param CreateRequest $request
     * @return Task
     */
    public function create(CreateRequest $request): Task
    {
        try {
            $task = Task::create([
                'title' => $request->title,
                'position_id' => $request->position
            ]);
        } catch (\Exception $e) {
            throw new \DomainException('Creating error');
        }
        return $task;
    }

    /**
     * @param Task $task
     * @param EditRequest $request
     * @return Task
     */
    public function edit(Task $task, EditRequest $request): Task
    {
        $task = $this->taskRepository->getById($task->id);
        try {
            $task->update([
                'title' => $request->title,
                'position_id' => $request->position
            ]);
        } catch (\Exception $e) {
            throw new \DomainException('Updating error');
        }
        return $task;
    }

    /**
     * @param Task $task
     */
    public function destroy(Task $task): void
    {
        $task = $this->taskRepository->getById($task->id);
        try {
            $task->delete();
        } catch (\Exception $e) {
            throw new \DomainException('Deleting error');
        }
    }
}
