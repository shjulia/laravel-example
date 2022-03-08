<?php

namespace App\Http\Controllers\Auth\Partner;

use App\Entities\User\Partner\Partner;
use App\Entities\User\User;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\AdditionalRequest;
use App\Http\Requests\Auth\Partner\UserBaseRequest;
use App\Http\Requests\Auth\Partner\UserDetailsRequest;
use App\Repositories\User\UserRepository;
use App\UseCases\Auth\Partner\RegisterService;
use Illuminate\Support\Facades\Auth;

/**
 * Class RegisterController
 * @package App\Http\Controllers\Auth\Distributor
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

    /** @var User|null */
    private $user;

    /**
     * RegisterController constructor.
     * @param RegisterService $service
     * @param UserRepository $userRepository
     */
    public function __construct(RegisterService $service, UserRepository $userRepository)
    {
        $this->middleware('guest')->except('success');
        $this->middleware(function ($request, $next) {
            try {
                $this->user = $this->userRepository->getPartnerByTmpCode($request->code);
                return $next($request);
            } catch (\DomainException $e) {
                return back()->with(['error' => $e->getMessage()]);
            }
        })->except(['userBaseSave', 'success']);
        $this->service = $service;
        $this->userRepository = $userRepository;
    }

    /**
     * @param UserBaseRequest $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function userBaseSave(UserBaseRequest $request)
    {
        try {
            $user = $this->service->userBaseSave($request);
        } catch (\Throwable $e) {
            return back()->with(['error' => 'User creating error']);
        }
        return redirect()->route('base.signup.details', ['code' => $user->tmp_token]);
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    /*public function simpleSuccess()
    {
        $user = $this->user;
        $route = route('base.signup.additional', ['code' => $user->tmp_token]);
        return view('register.simple-success', compact('user', 'route'));
    }*/

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    /*public function additional()
    {
        $user = $this->user;
        $route = route('base.signup.additionalSave', ['code' => $user->tmp_token]);
        return view('register.additional', compact('user', 'route'));
    }*/

    /**
     * @param AdditionalRequest $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    /*public function additionalSave(AdditionalRequest $request)
    {
        try {
            $user = $this->user;
            $this->service->saveAdditionalUserData($request, $user);
        } catch (\DomainException $e) {
            return back()->with(['error' => $e->getMessage()]);
        }

        return redirect()->route('base.signup.details', ['code' => $user->tmp_token]);
    }*/

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function details()
    {
        $user = $this->user;
        $descriptions = Partner::descriptionsList();
        return view('register.partner.details', compact('user', 'descriptions'));
    }

    /**
     * @param UserDetailsRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function userDetailsSave(UserDetailsRequest $request)
    {
        try {
            $data = $this->service->detailsSave($request, $this->user);
            $data = explode(':', $data);
            if ($data[0] == 'provider') {
                return redirect()->route('signup.industry', ['code' => $this->user->tmp_token]);
            } elseif ($data[0] == 'practice') {
                return redirect()->route('practice.signup.base', ['code' => $this->user->tmp_token]);
            }
            Auth::login($this->user);
        } catch (\DomainException $e) {
            return back()->with(['error' => $e->getMessage()]);
        }
        return redirect()->route('referral.index');
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function success()
    {
        return view('register.partner.success');
    }
}
