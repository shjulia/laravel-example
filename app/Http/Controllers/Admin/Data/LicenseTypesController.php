<?php

namespace App\Http\Controllers\Admin\Data;

use App\Entities\Data\LicenseType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Data\LicenseType\CreateRequest;
use App\Repositories\Data\LicenseTypesRepository;
use App\Repositories\Data\StatesRepository;
use App\Repositories\Industry\PositionRepository;
use App\UseCases\Admin\Manage\Data\Licenses\LicenseTypesService;
use Illuminate\Http\Request;

/**
 * Class LicenseTypesController
 * @package App\Http\Controllers\Admin\Data
 */
class LicenseTypesController extends Controller
{
    /**
     * @var LicenseTypesRepository
     */
    private $licenseTypesRepository;

    /**
     * @var LicenseTypesService
     */
    private $licenseTypesService;

    /**
     * @var PositionRepository
     */
    private $positionRepository;

    /**
     * @var StatesRepository
     */
    private $stateRepository;

    /**
     * LicenseTypesController constructor.
     * @param LicenseTypesRepository $licenseTypesRepository
     * @param LicenseTypesService $licenseTypesService
     * @param PositionRepository $positionRepository
     * @param StatesRepository $stateRepository
     */
    public function __construct(
        LicenseTypesRepository $licenseTypesRepository,
        LicenseTypesService $licenseTypesService,
        PositionRepository $positionRepository,
        StatesRepository $stateRepository
    ) {
        $this->licenseTypesRepository = $licenseTypesRepository;
        $this->licenseTypesService = $licenseTypesService;
        $this->positionRepository = $positionRepository;
        $this->stateRepository = $stateRepository;
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $licenseTypes = $this->licenseTypesRepository->findAll();
        return view('admin.data.license_types.index', compact('licenseTypes'));
    }

    /**
     * @param LicenseType $licenseType
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show(LicenseType $licenseType)
    {
        $licenseType = $this->licenseTypesRepository->getById($licenseType->id);
        return view('admin.data.license_types.show', compact('licenseType'));
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        $positions = $this->positionRepository->getAll();
        $states = $this->stateRepository->getAll();
        return view('admin.data.license_types.create', compact('positions', 'states'));
    }

    /**
     * @param CreateRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(CreateRequest $request)
    {
        try {
            $licenseType = $this->licenseTypesService->create($request);
        } catch (\DomainException $e) {
            return back()->with('error', $e->getMessage());
        }
        return redirect()->route('admin.data.license_types.show', $licenseType);
    }

    /**
     * @param LicenseType $licenseType
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit(LicenseType $licenseType)
    {
        $positions = $this->positionRepository->getAll();
        $states = $this->stateRepository->getAll();
        $licenseType = $this->licenseTypesRepository->getById($licenseType->id);
        $licenseTypeArray = $this->licenseTypesRepository->licenseTypeArray($licenseType);
        return view('admin.data.license_types.edit', compact('licenseType', 'positions', 'states', 'licenseTypeArray'));
    }

    /**
     * @param Request $request
     * @param LicenseType $licenseType
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(CreateRequest $request, LicenseType $licenseType)
    {
        try {
            $this->licenseTypesService->edit($licenseType, $request);
        } catch (\DomainException $e) {
            return back()->with('error', $e->getMessage());
        }
        return redirect()->route('admin.data.license_types.show', $licenseType);
    }

    /**
     * @param LicenseType $licenseType
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(LicenseType $licenseType)
    {
        try {
            $this->licenseTypesService->destroy($licenseType);
        } catch (\DomainException $e) {
            return back()->with('error', $e->getMessage());
        }
        return redirect()->route('admin.data.license_types.index');
    }
}
