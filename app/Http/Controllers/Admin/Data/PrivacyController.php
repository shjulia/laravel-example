<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\Data;

use App\Entities\Data\Privacy;
use App\Http\Controllers\Controller;
use App\Repositories\Data\PrivacyRepository;
use App\UseCases\Admin\Manage\Data\Tools\PrivacyService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PrivacyController extends Controller
{
    /**
     * @var PrivacyService
     */
    private $privacyService;

    public function __construct(PrivacyService $privacyService)
    {
        $this->privacyService = $privacyService;
    }

    /**
     * @param PrivacyRepository $privacyRepository
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(PrivacyRepository $privacyRepository)
    {
        $privacy = $privacyRepository->findAll();
        return view('admin.data.privacy.index', compact('privacy'));
    }

    /**
     * @param Privacy|null $privacy
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create(?Privacy $privacy = null)
    {
        return view('admin.data.privacy.create', compact('privacy'));
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
        $privacy = $this->privacyService->create($request->text, Auth::user());
        return redirect()->route('admin.data.privacy.show', $privacy);
    }

    /**
     * @param Privacy $privacy
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show(Privacy $privacy)
    {
        return view('admin.data.privacy.show', compact('privacy'));
    }
}
