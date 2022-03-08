<?php

namespace App\Http\Controllers\Admin\Data;

use App\Entities\Industry\Position;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Data\Position\CreateRequest;
use App\Http\Requests\Admin\Data\Position\EditRequest;
use App\Repositories\Industry\IndustryRepository;
use App\Repositories\Industry\PositionRepository;
use App\UseCases\Admin\Manage\Data\Positions\PositionService;
use Illuminate\Http\Request;

/**
 * Class PositionController
 * @package App\Http\Controllers\Admin\Data
 */
class PositionController extends Controller
{
    /**
     * @var PositionRepository
     */
    private $positionRepository;

    /**
     * @var PositionService
     */
    private $positionService;

    /**
     * @var IndustryRepository
     */
    private $industryRepository;

    /**
     * PositionController constructor.
     * @param PositionRepository $positionRepository
     * @param PositionService $positionService
     * @param IndustryRepository $industryRepository
     */
    public function __construct(
        PositionRepository $positionRepository,
        PositionService $positionService,
        IndustryRepository $industryRepository
    ) {
        $this->positionRepository = $positionRepository;
        $this->positionService = $positionService;
        $this->industryRepository = $industryRepository;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $positions = $this->positionRepository->findByQueryParams($request);
        $industries = $this->industryRepository->getAll();
        return view('admin.data.position.index', compact('positions', 'industries'));
    }

    /**
     * @param Position $position
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show(Position $position)
    {
        return view('admin.data.position.show', compact('position'));
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        $industries = $this->industryRepository->getAll();
        $positionsList = $this->positionRepository->getParents();
        return view('admin.data.position.create', compact('industries', 'positionsList'));
    }

    /**
     * @param CreateRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(CreateRequest $request)
    {
        try {
            $position = $this->positionService->create($request);
        } catch (\DomainException $e) {
            return back()->with('error', $e->getMessage());
        }
        return redirect()->route('admin.data.positions.show', $position);
    }

    /**
     * @param position $position
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit(Position $position)
    {
        $industries = $this->industryRepository->getAll();
        $positionsList = $this->positionRepository->getParents();
        return view('admin.data.position.edit', compact('position', 'industries', 'positionsList'));
    }

    /**
     * @param EditRequest $request
     * @param Position $position
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(EditRequest $request, Position $position)
    {
        try {
            $position = $this->positionService->edit($position, $request);
        } catch (\DomainException $e) {
            return back()->with('error', $e->getMessage());
        }
        return redirect()->route('admin.data.positions.show', $position);
    }

    /**
     * @param Position $position
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Position $position)
    {
        try {
            $this->positionService->destroy($position);
        } catch (\DomainException $e) {
            return back()->with('error', $e->getMessage());
        }
        return redirect()->route('admin.data.positions.index');
    }
}
