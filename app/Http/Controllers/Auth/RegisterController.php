<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Repositories\Industry\IndustryRepository;
use App\UseCases\Auth\AutoSaveService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * Class RegisterController
 * @package App\Http\Controllers\Auth
 */
class RegisterController extends Controller
{
    /**
     * @var IndustryRepository
     */
    private $industryRepository;

    /**
     * @var AutoSaveService
     */
    private $autoSaveService;

    /**
     * RegisterController constructor.
     * @param IndustryRepository $industryRepository
     * @param AutoSaveService $autoSaveService
     */
    public function __construct(IndustryRepository $industryRepository, AutoSaveService $autoSaveService)
    {
        $this->middleware('guest');
        $this->industryRepository = $industryRepository;
        $this->autoSaveService = $autoSaveService;
    }

    /**
     * @param null|string $industry
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function userBase(?string $industry = null)
    {
        $type = null;
        $code = null;
        $industry = $industry ? $this->industryRepository->getIDByIndustryAlias($industry) : null;
        return view('register.user-base', compact('industry', 'type', 'code'));
    }

    /**
     * @param null|string $type
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function userBaseDirect(?string $type = null)
    {
        $type = $type ? (in_array($type, ['provider', 'practice', 'partner']) ? $type : null) : null;
        $industry = null;
        $code = null;
        return view('register.user-base', compact('type', 'industry', 'code'));
    }

    /**
     * @param null|string $code
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function userBaseByInvite(Request $request, ?string $code = null)
    {
        $code = $code ? $request->code : null;
        $industry = null;
        $type = null;
        return view('register.user-base', compact('type', 'industry', 'type', 'code'));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function autoSave(Request $request)
    {
        try {
            $this->autoSaveService->save($request->email, $request->first_name, $request->last_name);
        } catch (\Exception $e) {
            \LogHelper::error($e);
            return response()->json(['error' => 'Error'], Response::HTTP_BAD_GATEWAY);
        }
        return response()->json([], Response::HTTP_OK);
    }
}
