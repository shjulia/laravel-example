<?php

namespace App\Http\Controllers\Admin\Data;

use App\Entities\Industry\Task;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Data\Task\CreateRequest;
use App\Http\Requests\Admin\Data\Task\EditRequest;
use App\Repositories\Industry\IndustryRepository;
use App\Repositories\Industry\PositionRepository;
use App\Repositories\Industry\TaskRepository;
use App\UseCases\Admin\Manage\Data\Positions\TaskService;
use Illuminate\Http\Request;

/**
 * Class TaskController
 * @package App\Http\Controllers\Admin\Data
 */
class TaskController extends Controller
{
    /**
     * @var TaskRepository
     */
    private $taskRepository;

    /**
     * @var TaskService
     */
    private $taskService;

    /**
     * @var IndustryRepository
     */
    private $positionRepository;

    /**
     * TaskController constructor.
     * @param TaskRepository $taskRepository
     * @param TaskService $taskService
     * @param PositionRepository $positionRepository
     */
    public function __construct(
        TaskRepository $taskRepository,
        TaskService $taskService,
        PositionRepository $positionRepository
    ) {
        $this->taskRepository = $taskRepository;
        $this->taskService = $taskService;
        $this->positionRepository = $positionRepository;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $tasks = $this->taskRepository->findByQuery();
        $positions = $this->positionRepository->getAll();
        return view('admin.data.task.index', compact('tasks', 'positions'));
    }

    /**
     * @param Task $task
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show(Task $task)
    {
        return view('admin.data.task.show', compact('task'));
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        $positions = $this->positionRepository->getAll();
        return view('admin.data.task.create', compact('positions'));
    }

    /**
     * @param CreateRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(CreateRequest $request)
    {
        try {
            $speciality = $this->taskService->create($request);
        } catch (\DomainException $e) {
            return back()->with('error', $e->getMessage());
        }
        return redirect()->route('admin.data.tasks.show', $speciality);
    }

    /**
     * @param Task $task
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit(Task $task)
    {
        $positions = $this->positionRepository->getAll();
        return view('admin.data.task.edit', compact('task', 'positions'));
    }

    /**
     * @param EditRequest $request
     * @param Task $task
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(EditRequest $request, Task $task)
    {
        try {
            $speciality = $this->taskService->edit($task, $request);
        } catch (\DomainException $e) {
            return back()->with('error', $e->getMessage());
        }
        return redirect()->route('admin.data.tasks.show', $speciality);
    }

    /**
     * @param Task $task
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Task $task)
    {
        try {
            $this->taskService->destroy($task);
        } catch (\DomainException $e) {
            return back()->with('error', $e->getMessage());
        }
        return redirect()->route('admin.data.tasks.index');
    }
}
