<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\Users\Practice;

use App\Entities\User\User;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\Practice\BaseInfoRequest;
use App\Http\Requests\Auth\Practice\InsuranceRequest;
use App\Http\Requests\Auth\Practice\UploadImageOrPDFRequest;
use App\Repositories\User\UserRepository;
use App\UseCases\Admin\Manage\Users\EditService;
use App\UseCases\Auth\Practice\RegisterService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

/**
 * Class EditController
 * @package App\Http\Controllers\Admin\Users\Practice
 */
class EditController extends Controller
{
    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @var RegisterService
     */
    private $registerService;

    /**
     * EditController constructor.
     * @param UserRepository $userRepository
     * @param RegisterService $registerService
     */
    public function __construct(
        UserRepository $userRepository,
        RegisterService $registerService
    ) {
        $this->registerService = $registerService;
        $this->middleware(function ($request, $next) {
            $this->registerService->setAdmin(Auth::user());
            return $next($request);
        });
        $this->userRepository = $userRepository;
    }

    /**
     * @param EditService $service
     * @param User $user
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function addRate(EditService $service, User $user, Request $request)
    {
        /** @var User $admin */
        $admin = Auth::user();
        try {
            $service->addRateToPractice($user->practice, (int)$request->rate, $admin);
        } catch (\DomainException $e) {
            return back()->with(['error' => $e->getMessage()]);
        }

        return back()->with(['success' => 'Rate have been added to practice successfully']);
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function base(User $user)
    {
        $tab = "practice-base";
        return view('admin.users.edit.practice.base', compact('user', 'tab'));
    }

    /**
     * @param BaseInfoRequest $request
     * @param User $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function baseSave(BaseInfoRequest $request, User $user)
    {
        try {
            $this->registerService->saveBaseInfo($request, $user, false);
        } catch (\DomainException $e) {
            return back()->with(['error' => $e->getMessage()]);
        }

        return back()->with(['success' => 'Practice info updated successfully']);
    }

    /**
     * @param User $user
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function insurance(User $user)
    {
        $tab = "insurance";
        return view('admin.users.edit.practice.insurance', compact('user', 'tab'));
    }

    /**
     * @param UploadImageOrPDFRequest $request
     * @param User $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function uploadInsurance(UploadImageOrPDFRequest $request, User $user)
    {
        $path = $this->registerService->uploadPolicyPhoto($request, $user);
        return response()->json($path, Response::HTTP_OK);
    }

    /**
     * @param User $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function removeInsurance(User $user)
    {
        $this->registerService->removePolicyPhoto($user);
        return response()->json([], Response::HTTP_OK);
    }

    /**
     * @param InsuranceRequest $request
     * @param User $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function insuranceSave(InsuranceRequest $request, User $user)
    {
        try {
            $this->registerService->saveInsurance($request, $user);
        } catch (\DomainException $e) {
            return back()->with(['error' => $e->getMessage()]);
        }
        return back()->with(['success' => 'Practice info updated successfully']);
    }
}
