<?php

namespace App\Http\Controllers\Auth\Provider;

use App\Entities\Data\LicenseType;
use App\Entities\Data\State;
use App\Entities\User\User;
use App\Http\Requests\Auth\Provider\LicenceRequest;
use App\Http\Requests\Auth\Provider\OneLicenseRequest;
use App\Http\Requests\Auth\Provider\UploadImageRequest;
use App\Repositories\Data\LicenseTypesRepository;
use App\Repositories\User\UserRepository;
use App\UseCases\Auth\Provider\RegisterService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

/**
 * Class LicensesController
 * @package App\Http\Controllers\Auth\Provider
 */
class LicensesController extends Controller
{
    /**
     * @var LicenseTypesRepository
     */
    private $licenseTypesRepository;

    /**
     * @var RegisterService
     */
    private $service;

    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * LicensesController constructor.
     * @param LicenseTypesRepository $licenseTypesRepository
     * @param RegisterService $service
     * @param UserRepository $userRepository
     */
    public function __construct(
        LicenseTypesRepository $licenseTypesRepository,
        RegisterService $service,
        UserRepository $userRepository
    ) {
        $this->middleware(['auth', 'can:provider-account-details']);
        $this->licenseTypesRepository = $licenseTypesRepository;
        $this->service = $service;
        $this->userRepository = $userRepository;
    }

    /**
     * Show license form
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showForm()
    {
        /** @var User $user */
        $user = auth()->user();
        $user = $this->userRepository->getUserWithFullData($user);
        $states = State::all();
        if (!$user->specialist->driver_state) {
            return back()->with(['error' => 'Address must be set.']);
        }
        $types = $this->licenseTypesRepository->findByPositionAndState(
            $user->specialist->position_id,
            $user->specialist->driver_state
        );
        return view('license.license', compact('user', 'types', 'states'));
    }

    /**
     * Create provider license
     *
     * @param LicenceRequest $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    protected function create(LicenceRequest $request)
    {
        try {
            $this->service->licenseSave($request, auth()->user());
        } catch (\DomainException $e) {
            return back()->with(['error' => $e->getMessage()]);
        }

        return redirect(route('my-licenses'))->with(['success' => 'Information updated successfully']);
    }

    /**
     * @param UploadImageRequest $request
     * @return string
     */
    public function uploadMedicalLicense(UploadImageRequest $request)
    {
        $path = $this->service->uploadMedicalLicense($request, auth()->user());
        return response()->json($path, Response::HTTP_OK);
    }

    /**
     * @param OneLicenseRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function oneLicenseSave(OneLicenseRequest $request)
    {
        try {
            $user = Auth::user();
            $this->service->oneLicenseSave($request, $user);
        } catch (\DomainException $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_OK);
        }

        return response()->json([], Response::HTTP_OK);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function removeLicense(Request $request)
    {
        try {
            /** @var User $user */
            $user = auth()->user();
            $this->service->licenseRemove($request->position, $user);
        } catch (\DomainException $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_BAD_GATEWAY);
        }
        return response()->json([], Response::HTTP_OK);
    }
}
