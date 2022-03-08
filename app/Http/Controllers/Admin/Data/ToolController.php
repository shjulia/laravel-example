<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\Data;

use App\Entities\Data\Tool;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Data\Tool\CreateRequest;
use App\Http\Requests\Admin\Data\Tool\EditRequest;
use App\Repositories\Data\ToolRepository;
use App\UseCases\Admin\Manage\Data\Tools\ToolService;
use Illuminate\Http\Request;

/**
 * Class ToolController
 * @package App\Http\Controllers\Admin\Data
 */
class ToolController extends Controller
{
    /**
     * @var ToolService
     */
    private $toolService;
    /**
     * @var ToolRepository
     */
    private $tools;

    /**
     * ToolController constructor.
     * @param ToolService $toolService
     * @param ToolRepository $tools
     */
    public function __construct(
        ToolService $toolService,
        ToolRepository $tools
    ) {
        $this->toolService = $toolService;
        $this->tools = $tools;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $tools = $this->tools->findPaginate();
        return view('admin.data.tool.index', compact('tools'));
    }

    /**
     * @param Tool $tool
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show(Tool $tool)
    {
        return view('admin.data.tool.show', compact('tool'));
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        return view('admin.data.tool.create');
    }

    /**
     * @param CreateRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(CreateRequest $request)
    {
        try {
            $tool = $this->toolService->create($request->title);
        } catch (\DomainException $e) {
            return back()->with('error', $e->getMessage());
        }
        return redirect()->route('admin.data.tools.index');
    }

    /**
     * @param Tool $tool
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit(Tool $tool)
    {
        return view('admin.data.tool.edit', compact('tool'));
    }

    /**
     * @param EditRequest $request
     * @param Tool $tool
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(EditRequest $request, Tool $tool)
    {
        try {
            $this->toolService->edit($tool, $request->title);
        } catch (\DomainException $e) {
            return back()->with('error', $e->getMessage());
        }
        return redirect()->route('admin.data.tools.index');
    }

    /**
     * @param Tool $tool
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Tool $tool)
    {
        try {
            $this->toolService->destroy($tool);
        } catch (\DomainException $e) {
            return back()->with('error', $e->getMessage());
        }
        return redirect()->route('admin.data.tools.index');
    }
}
