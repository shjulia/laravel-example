<?php

namespace App\Http\Controllers\Auth\Provider;

use App\Entities\User\User;
use App\Exceptions\User\SSNIsAlreadyRegisteredException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\AdditionalRequest;
use App\Http\Requests\Auth\Provider\AuthorizationRequests;
use App\Http\Requests\Auth\Provider\IdentityEditRequest;
use App\Http\Requests\Auth\Provider\OneLicenseRequest;
use App\Http\Requests\Auth\Provider\UserBaseRequest;
use App\Http\Requests\Auth\Provider\IndustryRequest;
use App\Http\Requests\Auth\Provider\IdentityRequest;
use App\Http\Requests\Auth\Provider\LicenceRequest;
use App\Http\Requests\Auth\Provider\CheckRequest;
use App\Http\Requests\Auth\Provider\UploadImageRequest;
use App\Repositories\Data\LicenseTypesRepository;
use App\Repositories\Data\Location\AreaRepository;
use App\Repositories\Industry\IndustryRepository;
use App\Repositories\Industry\PositionRepository;
use App\Repositories\User\UserRepository;
use App\Services\Maps\AutocompletePlaceService;
use App\UseCases\Auth\Provider\RegisterService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

/**
 * Class RegisterController
 * @package App\Http\Controllers\Auth\Provider
 */
class RegisterController extends Controller
{
    /**
     * @var RegisterService
     */
    private $service;

    /**
     * @var AutocompletePlaceService
     */
    private $autocompletePlaceService;

    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @var IndustryRepository
     */
    private $industryRepository;

    /**
     * @var PositionRepository
     */
    private $positionRepository;

    /**
     * @var LicenseTypesRepository
     */
    private $licenseTypesRepository;

    /** @var AreaRepository $areaRepository */
    private $areaRepository;

    /** @var User|null */
    private $user;

    /**
     * RegisterController constructor.
     * @param RegisterService $service
     * @param UserRepository $userRepository
     * @param IndustryRepository $industryRepository
     * @param PositionRepository $positionRepository
     * @param LicenseTypesRepository $licenseTypesRepository
     * @param AutoCompletePlaceService $autocompletePlaceService
     * @param AreaRepository $areaRepository
     */
    public function __construct(
        RegisterService $service,
        UserRepository $userRepository,
        IndustryRepository $industryRepository,
        PositionRepository $positionRepository,
        LicenseTypesRepository $licenseTypesRepository,
        AutoCompletePlaceService $autocompletePlaceService,
        AreaRepository $areaRepository
    ) {
        $this->middleware('guest')->except('success');
        $this->middleware(function ($request, $next) {
            try {
                $this->user = $this->userRepository->getProviderByTmpCode($request->code);
                return $next($request);
            } catch (\DomainException $e) {
                return back()->with(['error' => $e->getMessage()]);
            }
        })->except(['userBaseSave', 'success', 'autocomplete', 'placeData']);
        $this->service = $service;
        $this->autocompletePlaceService = $autocompletePlaceService;
        $this->userRepository = $userRepository;
        $this->industryRepository = $industryRepository;
        $this->positionRepository = $positionRepository;
        $this->licenseTypesRepository = $licenseTypesRepository;
        $this->areaRepository = $areaRepository;
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
            \LogHelper::error($e);
            return back()->with(['error' => 'User creating error']);
        }
        return redirect()->route('signup.industry', ['code' => $user->tmp_token]);
    }

    /*public function simpleSuccess()
    {
        $user = $this->user;
        $route = route('signup.additional', ['code' => $user->tmp_token]);
        return view('register.simple-success', compact('user', 'route'));
    }*/

    /*public function additional()
    {
        $user = $this->user;
        $route = route('signup.additionalSave', ['code' => $user->tmp_token]);
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

        return redirect()->route('signup.industry', ['code' => $user->tmp_token]);
    }*/


    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function industry()
    {
        $user = $this->user;
        $industries = $this->industryRepository->getAll();
        $positions = $this->positionRepository->getAllWithChildren();
        return view('register.provider.industry', compact('user', 'positions', 'industries'));
    }

    /**
     * @param IndustryRequest $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function industrySave(IndustryRequest $request)
    {
        try {
            $user = $this->user;
            $this->service->industrySave($request, $user);
        } catch (\DomainException $e) {
            return back()->with(['error' => $e->getMessage()]);
        }
        return redirect()->route('need', ['code' => $user->tmp_token]);
        //return redirect()->route('signup.identity', ['code' => $user->tmp_token]);
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function identity()
    {
        $user = $this->user;
        return view('register.provider.identity', compact('user'));
    }

    /**
     * @param IdentityRequest $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function identitySave(IdentityRequest $request)
    {
        try {
            $user = $this->user;
            $this->service->identitySave($request, $user);
        } catch (\DomainException $e) {
            return back()->with(['error' => $e->getMessage()]);
        }

        return redirect(route('signup.license', ['code' => $user->tmp_token]));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function phone(Request $request)
    {
        try {
            $user = $this->user;
            if ($user->phone !== $request->phone) {
                $this->service->setPhone($request->phone, $user);
            }
        } catch (\DomainException $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_BAD_GATEWAY);
        }
        return response()->json([], Response::HTTP_OK);
    }

    /**
     * @param UploadImageRequest $request
     * @return array
     * @throws \Exception
     */
    public function uploadDriverLicense(UploadImageRequest $request)
    {
        $user = $this->user;
        $path = $this->service->uploadDriverLicense($request, $user);
        //$result = $this->service->analyzeImage($pathes, $user);
        $result = $this->service->analyzeImage($path, $user);
        try {
            $area = $this->areaRepository->findByCityOrZip(
                $result['address']['state'],
                $result['address']['city'],
                $result['address']['zip']
            );
        } catch (\Exception $e) {
            \LogHelper::error($e, ['userId' => $user->id]);
        }

        if (isset($area)) {
            $result['market_open'] = $area->isOpen();
            $result['area_name'] = $area->name;
        }

        return response()->json($result, Response::HTTP_OK);
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function license()
    {
        $user = $this->user;
        if (!$user->specialist->driver_state) {
            return redirect()->route('signup.identity', ['code' => $user->tmp_token]);
        }
        $types = $this->licenseTypesRepository->findByPositionAndState(
            $user->specialist->position_id,
            $user->specialist->driver_state
        );
        if (empty($types['requiredLicense']) && empty($types['anotherLicense'])) {
            return redirect()->route('signup.check', ['code' => $user->tmp_token]);
        }
        return view('register.provider.license', compact('user', 'types'));
    }

    /**
     * @param UploadImageRequest $request
     * @return string
     */
    public function uploadMedicalLicense(UploadImageRequest $request)
    {
        $user = $this->user;
        $path = $this->service->uploadMedicalLicense($request, $user);
        return response()->json($path, Response::HTTP_OK);
    }

    /**
     * @param OneLicenseRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function oneLicenseSave(OneLicenseRequest $request)
    {
        try {
            $user = $this->user;
            $this->service->oneLicenseSave($request, $user);
        } catch (\DomainException $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_OK);
        }

        return response()->json([], Response::HTTP_OK);
    }

    /**
     * @param LicenceRequest $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function licenseSave(LicenceRequest $request)
    {
        try {
            $user = $this->user;
            $this->service->licenseSave($request, $user);
        } catch (\DomainException $e) {
            return back()->with(['error' => $e->getMessage()]);
        }

        return redirect()->route('signup.check', ['code' => $user->tmp_token]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function removeLicense(Request $request)
    {
        try {
            $user = $this->user;
            $this->service->licenseRemove($request->position, $user);
        } catch (\DomainException $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_BAD_GATEWAY);
        }
        return response()->json([], Response::HTTP_OK);
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function check()
    {
        $user = $this->user;
        $this->user->specialist->setAppends(['ssnVal']);
        return view('register.provider.check', compact('user'));
    }

    /**
     * @param IdentityEditRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function identityEdit(IdentityEditRequest $request)
    {
        try {
            $user = $this->user;
            $this->service->identityEdit($request, $user);
        } catch (\DomainException $e) {
            response()->json(['error' => $e->getMessage()], Response::HTTP_BAD_GATEWAY);
        }

        return response()->json([], Response::HTTP_OK);
    }

    /**
     * @param CheckRequest $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function checkSave(CheckRequest $request)
    {
        try {
            $user = $this->user;
            $this->service->checkSave($request, $user);
        } catch (\DomainException | SSNIsAlreadyRegisteredException $e) {
            return back()->with(['error' => $e->getMessage()]);
        }
        return redirect()->route('signup.disclosure', ['code' => $user->tmp_token]);
        //Auth::login($user);
        //return redirect()->route('signup.success');
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function disclosure()
    {
        $user = $this->user;
        return view('register.provider.disclosure', compact('user'));
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    public function disclosureAccept()
    {
        $user = $this->user;
        $routeName = "signup.authorization";
        if ($user->specialist->driver_state == "CA") {
            $routeName = "signup.stateDisclosure";
        }
        return redirect()->route($routeName, ['code' => $user->tmp_token]);
    }

    public function stateDisclosure()
    {
        $user = $this->user;
        return view('register.provider.state-disclosure', compact('user'));
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    public function stateDisclosureAccept()
    {
        $user = $this->user;
        return redirect()->route('signup.authorization', ['code' => $user->tmp_token]);
    }

    public function authorization()
    {
        $user = $this->user;
        return view('register.provider.authorization', compact('user'));
    }

    /**
     * @param AuthorizationRequests $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function authorizationAccept(AuthorizationRequests $request)
    {
        $user = $this->user;
        try {
            $this->service->lastStep($user);
        } catch (\DomainException $e) {
            return back()->with(['error' => $e->getMessage()]);
        }
        Auth::login($user);
        return redirect()->route('signup.success');
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function success()
    {
        $user = Auth::user();
        return view('register.provider.success', compact('user'));
    }

    /**
     * @param string $query
     * @param null|string $lat
     * @param null|string $lng
     * @return \Illuminate\Http\JsonResponse
     */
    public function autocomplete(string $query, ?string $lat = null, ?string $lng = null)
    {
        $res = $this->autocompletePlaceService->getPlacesNamesByQuery($query, $lat, $lng, false);
        return response()->json($res, Response::HTTP_OK);
    }

    /**
     * Get place data from Google by id
     *
     * @param int $place_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function placeData(string $place_id)
    {
        $res = $this->autocompletePlaceService->getPlaceData($place_id);
        return response()->json($res, Response::HTTP_OK);
    }
}
