<?php

namespace App\Http\Controllers\Auth\Practice;

use App\Entities\User\User;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\AdditionalRequest;
use App\Http\Requests\Auth\Practice\BaseInfoRequest;
use App\Http\Requests\Auth\Practice\IndustryRequest;
use App\Http\Requests\Auth\Practice\InsuranceRequest;
use App\Http\Requests\Auth\Practice\UploadImageOrPDFRequest;
use App\Http\Requests\Auth\Practice\UserBaseRequest;
use App\Repositories\Industry\IndustryRepository;
use App\Repositories\User\UserRepository;
use App\UseCases\Auth\Practice\RegisterService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

/**
 * Class RegisterController
 * @package App\Http\Controllers\Auth\Practice
 */
class RegisterController extends Controller
{
    /**
     * @var RegisterService
     */
    private $service;

    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @var IndustryRepository
     */
    private $industryRepository;

    /**
     * @var User|null
     */
    private $user;

    /**
     * RegisterController constructor.
     * @param RegisterService $service
     * @param UserRepository $userRepository
     * @param IndustryRepository $industryRepository
     */
    public function __construct(
        RegisterService $service,
        UserRepository $userRepository,
        IndustryRepository $industryRepository
    ) {
        $this->middleware('guest')->except(['success', 'autocomplete', 'placeData']);
        $this->middleware(function ($request, $next) {
            try {
                $this->user = $this->userRepository->getPracticeByTmpCode($request->code);
                return $next($request);
            } catch (\DomainException $e) {
                return back()->with(['error' => $e->getMessage()]);
            }
        })->except(['userBaseSave', 'success', 'autocomplete', 'placeData']);
        $this->service = $service;
        $this->userRepository = $userRepository;
        $this->industryRepository = $industryRepository;
    }

    /**
     * @param UserBaseRequest $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function userBaseSave(UserBaseRequest $request)
    {
        try {
            $user = $this->service->saveUserBase($request);
        } catch (\Throwable $e) {
            return back()->with(['error' => 'User creating error']);
        }
        if ($user->practice->industry_id) {
            return redirect(route('practice.signup.base', ['code' => $user->tmp_token]));
        }
        return redirect()->route('practice.signup.industry', ['code' => $user->tmp_token]);
    }

    /*public function simpleSuccess()
    {
        $user = $this->user;
        $route = route('practice.signup.additional', ['code' => $user->tmp_token]);
        return view('register.simple-success', compact('user', 'route'));
    }*/

    /*public function additional()
    {
        $user = $this->user;
        $route = route('practice.signup.additionalSave', ['code' => $user->tmp_token]);
        return view('register.additional', compact('user', 'route'));
    }*/

    /*public function additionalSave(AdditionalRequest $request)
    {
        try {
            $user = $this->user;
            $this->service->saveAdditionalUserData($request, $user);
        } catch (\DomainException $e) {
            return back()->with(['error' => $e->getMessage()]);
        }

        return redirect()->route('practice.signup.industry', ['code' => $user->tmp_token]);
    }*/

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function industry()
    {
        $user = $this->user;
        $industries = $this->industryRepository->getAll();
        return view('register.practice.industry', compact('user', 'industries'));
    }

    /**
     * @param IndustryRequest $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function industrySave(IndustryRequest $request)
    {
        try {
            $user = $this->user;
            $this->service->saveIndustry($request, $user);
        } catch (\DomainException $e) {
            return back()->with(['error' => $e->getMessage()]);
        }

        return redirect()->route('practice.signup.base', ['code' => $user->tmp_token]);
    }

    /**
     * @param string $query
     * @param null|string $lat
     * @param null|string $lng
     * @return \Illuminate\Http\JsonResponse
     */
    public function autocomplete(string $query, ?string $lat = null, ?string $lng = null)
    {
        $res = $this->service->autocompleteQuery($query, $lat, $lng);
        return response()->json($res, Response::HTTP_OK);
    }

    /**
     * @param string $placeId
     * @return \Illuminate\Http\JsonResponse
     */
    public function placeData(string $placeId)
    {
        $res = $this->service->getPlaceData($placeId);
        return response()->json($res, Response::HTTP_OK);
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function base()
    {
        $user = $this->user;
        return view('register.practice.base', compact('user'));
    }

    /**
     * @param BaseInfoRequest $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function baseSave(BaseInfoRequest $request)
    {
        try {
            $user = $this->user;
            $this->service->saveBaseInfo($request, $user);
        } catch (\DomainException $e) {
            return back()->with(['error' => $e->getMessage()]);
        }

        return redirect()->route('practice.signup.insurance', ['code' => $user->tmp_token]);
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function insurance()
    {
        $user = $this->user;
        return view('register.practice.insurance', compact('user'));
    }

    /**
     * @param UploadImageOrPDFRequest $request
     * @return string
     */
    public function uploadInsurance(UploadImageOrPDFRequest $request)
    {
        $user = $this->user;
        $path = $this->service->uploadPolicyPhoto($request, $user);
        return $path;
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function removeInsurance()
    {
        $user = $this->user;
        $this->service->removePolicyPhoto($user);
        return response()->json([], Response::HTTP_OK);
    }

    /**
     * @param InsuranceRequest $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function insuranceSave(InsuranceRequest $request)
    {
        try {
            $user = $this->user;
            $this->service->saveInsurance($request, $user);
            Auth::login($user);
        } catch (\DomainException $e) {
            return back()->with(['error' => $e->getMessage()]);
        }

        return redirect()->route('practice.signup.success');
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function success()
    {
        $user = Auth::user();
        return view('register.practice.success', compact('user'));
    }
}
