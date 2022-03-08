<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\Users\Provider;

use App\Entities\User\User;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\User\Edit\LicenceRequest;
use App\Http\Requests\Admin\User\Edit\PositionRequest;
use App\Http\Requests\Admin\User\UpdateRequest;
use App\Http\Requests\Auth\Provider\IdentityRequest;
use App\Http\Requests\Auth\Provider\Onboarding\RateRequest;
use App\Http\Requests\Auth\Provider\OneLicenseRequest;
use App\Http\Requests\Auth\Provider\UploadImageRequest;
use App\Repositories\Data\LicenseTypesRepository;
use App\Repositories\Industry\PositionRepository;
use App\Repositories\User\UserRepository;
use App\UseCases\Admin\Manage\Users\EditService;
use App\UseCases\Auth\Provider\OnboardingService;
use App\UseCases\Auth\Provider\RegisterService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

/**
 * Class EditController
 * @package App\Http\Controllers\Admin\Users
 */
class EditController extends Controller
{
    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @var EditService
     */
    private $usersService;

    /**
     * @var RegisterService
     */
    private $registerService;

    /**
     * EditController constructor.
     * @param UserRepository $userRepository
     * @param EditService $usersService
     * @param RegisterService $registerService
     */
    public function __construct(
        UserRepository $userRepository,
        EditService $usersService,
        RegisterService $registerService
    ) {
        $this->registerService = $registerService;
        $this->middleware(function ($request, $next) {
            $this->registerService->setAdmin(Auth::user());
            return $next($request);
        });
        $this->userRepository = $userRepository;
        $this->usersService = $usersService;
    }

    /**
     * @param User $user
     * @param PositionRepository $positionRepository
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function position(User $user, PositionRepository $positionRepository)
    {
        $tab = "position";
        $groupedPositions = $positionRepository->findGroupedWithChildren();
        return view('admin.users.edit.provider.position', compact('user', 'tab', 'groupedPositions'));
    }

    /**
     * @param PositionRequest $request
     * @param User $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function positionEdit(PositionRequest $request, User $user)
    {
        $admin = Auth::user();
        try {
            $this->usersService->editPosition($request, $user, $admin);
        } catch (\DomainException $e) {
            return back()->with(['error' => $e->getMessage()]);
        }
        return back()->with(['success' => 'Provider info updated successfully']);
    }

    /**
     * @param User $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function identityDelete(User $user)
    {
        try {
            $this->registerService->identityRemove($user);
        } catch (\DomainException $e) {
            return back()->with(['error' => $e->getMessage()]);
        }
        return back()->with(['success' => 'Identity changes successfully']);
    }

    /**
     * @param Request $request
     * @param User $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function phone(Request $request, User $user)
    {
        try {
            if ($user->phone !== $request->phone) {
                $this->registerService->setPhone($request->phone, $user);
            }
        } catch (\DomainException $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_BAD_GATEWAY);
        }
        return response()->json([], Response::HTTP_OK);
    }

    /**
     * @param UploadImageRequest $request
     * @param User $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function uploadDriverLicense(UploadImageRequest $request, User $user)
    {
        $path = $this->registerService->uploadDriverLicense($request, $user);
        //$result = $this->service->analyzeImage($pathes, $user);
        $result = $this->registerService->analyzeImage($path, $user);

        return response()->json($result, Response::HTTP_OK);
    }

    /**
     * @param User $user
     * @param LicenseTypesRepository $licenseTypesRepository
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function licenses(User $user, LicenseTypesRepository $licenseTypesRepository)
    {
        /** @var User $user */
        $user = $this->userRepository->getUserWithFullData($user);
        if (!$user->specialist->driver_state) {
            return back()->with(['error' => 'Address must be set.']);
        }
        $types = $licenseTypesRepository->findByPositionAndState(
            $user->specialist->position_id,
            $user->specialist->driver_state
        );
        $tab = "licenses";
        return view('admin.users.edit.provider.license', compact('user', 'types', 'tab'));
    }

    /**
     * @param OneLicenseRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function oneLicenseSave(OneLicenseRequest $request, User $user)
    {
        try {
            $this->registerService->oneLicenseSave($request, $user);
        } catch (\DomainException $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_OK);
        }

        return response()->json([], Response::HTTP_OK);
    }

    /**
     * @param LicenceRequest $request
     * @param User $user
     * @param RegisterService $registerService
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function licensesEdit(LicenceRequest $request, User $user)
    {
        try {
            $this->registerService->licenseSave($request, $user);
        } catch (\DomainException $e) {
            return back()->with(['error' => $e->getMessage()]);
        }
        return back()->with(['success' => 'Provider info updated successfully']);
    }

    /**
     * @param UploadImageRequest $request
     * @param User $user
     * @return string
     */
    public function uploadMedicalLicense(UploadImageRequest $request, User $user)
    {
        $path = $this->registerService->uploadMedicalLicense($request, $user);
        return response()->json($path, Response::HTTP_OK);
    }

    /**
     * @param Request $request
     * @param User $user
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function removeLicense(Request $request, User $user)
    {
        try {
            $this->registerService->licenseRemove($request->position, $user);
        } catch (\DomainException $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_BAD_GATEWAY);
        }
        return response()->json([], Response::HTTP_OK);
    }

    /**
     * @param User $user
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function check(User $user)
    {
        $tab = "check";
        return view('admin.users.edit.provider.check', compact('user', 'tab'));
    }

    /**
     * @param Request $request
     * @param User $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function checkEdit(Request $request, User $user)
    {
        $admin = Auth::user();
        try {
            $this->usersService->editSsn($request->ssn, $user, $admin);
        } catch (\DomainException $e) {
            return back()->with(['error' => $e->getMessage()]);
        }
        return back()->with(['success' => 'Provider info updated successfully']);
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function rate(User $user)
    {
        $positionRate = $user->specialist->position->fee;
        $tab = "rate";
        return view('admin.users.edit.provider.rate', compact('user', 'positionRate', 'tab'));
    }

    /**
     * @param User $user
     * @param RateRequest $request
     * @param OnboardingService $onboardingService
     * @return \Illuminate\Http\RedirectResponse
     */
    public function rateSave(User $user, RateRequest $request, OnboardingService $onboardingService)
    {
        /** @var User $admin */
        $admin = Auth::user();
        try {
            $onboardingService->setRate($user, (float)$request->rate, $admin);
        } catch (\DomainException $e) {
            return back()->with(['error' => $e->getMessage()]);
        }
        return back()->with(['success' => 'Provider rate updated successfully']);
    }
}
