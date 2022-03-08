<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\Data;

use App\Entities\Data\Term;
use App\Http\Controllers\Controller;
use App\Repositories\Data\TermsRepository;
use App\UseCases\Admin\Manage\Data\TermsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Class TermsController
 * @package App\Http\Controllers\Admin\Data
 */
class TermsController extends Controller
{
    /**
     * @var TermsService
     */
    private $termsService;

    /**
     * TermsController constructor.
     * @param TermsService $termsService
     */
    public function __construct(TermsService $termsService)
    {
        $this->termsService = $termsService;
    }

    /**
     * @param TermsRepository $termsRepository
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(TermsRepository $termsRepository)
    {
        $terms = $termsRepository->findAll();
        return view('admin.data.terms.index', compact('terms'));
    }

    /**
     * @param Term|null $term
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create(?Term $term = null)
    {
        return view('admin.data.terms.create', compact('term'));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        if (!$request->text) {
            return back()->with(['error' => 'Text must be set.']);
        }
        $terms = $this->termsService->create($request->text, Auth::user());
        return redirect()->route('admin.data.terms.show', $terms);
    }

    /**
     * @param Term $term
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show(Term $term)
    {
        return view('admin.data.terms.show', compact('term'));
    }
}
