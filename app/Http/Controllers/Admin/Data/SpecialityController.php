<?php

namespace App\Http\Controllers\Admin\Data;

use App\Entities\Industry\Speciality;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Data\Speciality\CreateRequest;
use App\Http\Requests\Admin\Data\Speciality\EditRequest;
use App\Repositories\Industry\IndustryRepository;
use App\Repositories\Industry\SpecialityRepository;
use App\UseCases\Admin\Manage\Data\Specialities\SpecialityService;
use Illuminate\Http\Request;

/**
 * Class SpecialityController
 * @package App\Http\Controllers\Admin\Data
 */
class SpecialityController extends Controller
{
    /**
     * @var SpecialityRepository
     */
    private $specialityRepository;

    /**
     * @var SpecialityService
     */
    private $specialityService;

    /**
     * @var IndustryRepository
     */
    private $industryRepository;

    /**
     * SpecialityController constructor.
     * @param SpecialityRepository $specialityRepository
     * @param SpecialityService $specialityService
     * @param IndustryRepository $industryRepository
     */
    public function __construct(
        SpecialityRepository $specialityRepository,
        SpecialityService $specialityService,
        IndustryRepository $industryRepository
    ) {
        $this->specialityRepository = $specialityRepository;
        $this->specialityService = $specialityService;
        $this->industryRepository = $industryRepository;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $specialities = $this->specialityRepository->findByQueryParams($request);
        $industries = $this->industryRepository->getAll();
        return view('admin.data.speciality.index', compact('specialities', 'industries'));
    }

    /**
     * @param Speciality $speciality
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show(Speciality $speciality)
    {
        return view('admin.data.speciality.show', compact('speciality'));
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        $industries = $this->industryRepository->getAll();
        return view('admin.data.speciality.create', compact('industries'));
    }

    /**
     * @param CreateRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(CreateRequest $request)
    {
        try {
            $speciality = $this->specialityService->create($request);
        } catch (\DomainException $e) {
            return back()->with('error', $e->getMessage());
        }
        return redirect()->route('admin.data.specialities.show', $speciality);
    }

    /**
     * @param speciality $speciality
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit(speciality $speciality)
    {
        $industries = $this->industryRepository->getAll();
        return view('admin.data.speciality.edit', compact('speciality', 'industries'));
    }

    /**
     * @param EditRequest $request
     * @param Speciality $speciality
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(EditRequest $request, Speciality $speciality)
    {
        try {
            $speciality = $this->specialityService->edit($speciality, $request);
        } catch (\DomainException $e) {
            return back()->with('error', $e->getMessage());
        }
        return redirect()->route('admin.data.specialities.show', $speciality);
    }

    /**
     * @param Speciality $speciality
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Speciality $speciality)
    {
        try {
            $this->specialityService->destroy($speciality);
        } catch (\DomainException $e) {
            return back()->with('error', $e->getMessage());
        }
        return redirect()->route('admin.data.specialities.index');
    }
}
