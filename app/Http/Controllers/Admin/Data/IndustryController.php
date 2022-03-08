<?php

namespace App\Http\Controllers\Admin\Data;

use App\Entities\Industry\Industry;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Data\Industry\CreateRequest;
use App\Http\Requests\Admin\Data\Industry\EditRequest;
use App\Repositories\Industry\IndustryRepository;
use App\UseCases\Admin\Manage\Data\Industries\IndustryService;
use Illuminate\Http\Request;

/**
 * Class IndustryController
 * @package App\Http\Controllers\Admin\Data
 */
class IndustryController extends Controller
{
    /**
     * @var IndustryRepository
     */
    private $industryRepository;

    /**
     * @var IndustryService
     */
    private $industryService;

    /**
     * IndustryController constructor.
     * @param IndustryRepository $industryRepository
     * @param IndustryService $industryService
     */
    public function __construct(
        IndustryRepository $industryRepository,
        IndustryService $industryService
    ) {
        $this->industryRepository = $industryRepository;
        $this->industryService = $industryService;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $industries = $this->industryRepository->findByQueryParams($request);
        return view('admin.data.industry.index', compact('industries'));
    }

    /**
     * @param Industry $industry
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show(Industry $industry)
    {
        return view('admin.data.industry.show', compact('industry'));
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        return view('admin.data.industry.create');
    }

    /**
     * @param CreateRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(CreateRequest $request)
    {
        try {
            $industry = $this->industryService->create($request);
        } catch (\DomainException $e) {
            return back()->with('error', $e->getMessage());
        }
        return redirect()->route('admin.data.industries.show', $industry);
    }

    /**
     * @param Industry $industry
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit(Industry $industry)
    {
        return view('admin.data.industry.edit', compact('industry'));
    }

    /**
     * @param EditRequest $request
     * @param Industry $industry
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(EditRequest $request, Industry $industry)
    {
        try {
            $industry = $this->industryService->edit($industry, $request);
        } catch (\DomainException $e) {
            return back()->with('error', $e->getMessage());
        }
        return redirect()->route('admin.data.industries.show', $industry);
    }

    /**
     * @param Industry $industry
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Industry $industry)
    {
        try {
            $this->industryService->destroy($industry);
        } catch (\DomainException $e) {
            return back()->with('error', $e->getMessage());
        }
        return redirect()->route('admin.data.industries.index');
    }
}
