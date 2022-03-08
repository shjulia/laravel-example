<?php

namespace App\Http\Controllers\Api\Auth\Provider;

use App\Entities\User\User;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\AdditionalRequest;
use App\Http\Requests\Auth\Provider\CheckRequest;
use App\Http\Requests\Auth\Provider\IdentityRequest;
use App\Http\Requests\Auth\Provider\IndustryRequest;
use App\Http\Requests\Auth\Provider\LicenceRequest;
use App\Http\Requests\Auth\Provider\UploadImageRequest;
use App\Http\Requests\Auth\Provider\UserBaseRequest;
use App\Repositories\Data\LicenseTypesRepository;
use App\Repositories\Industry\IndustryRepository;
use App\Repositories\Industry\PositionRepository;
use App\Repositories\User\UserRepository;
use App\UseCases\Auth\Provider\RegisterService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * Class RegisterController
 * @package App\Http\Controllers\Api\Auth\Provider
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
     * @var PositionRepository
     */
    private $positionRepository;

    /**
     * @var LicenseTypesRepository
     */
    private $licenseTypesRepository;

    /** @var User|null */
    private $user;

    /**
     * RegisterController constructor.
     * @param RegisterService $service
     * @param UserRepository $userRepository
     * @param IndustryRepository $industryRepository
     * @param PositionRepository $positionRepository
     * @param LicenseTypesRepository $licenseTypesRepository
     */
    public function __construct(
        RegisterService $service,
        UserRepository $userRepository,
        IndustryRepository $industryRepository,
        PositionRepository $positionRepository,
        LicenseTypesRepository $licenseTypesRepository
    ) {
        $this->middleware('guest');
        $this->middleware(function ($request, $next) {
            try {
                $this->user = $this->userRepository->getProviderByTmpCode($request->code);
                return $next($request);
            } catch (\DomainException $e) {
                return response()->json(['error' => $e->getMessage()], Response::HTTP_BAD_GATEWAY);
            }
        })->except(['userBaseSave']);
        $this->service = $service;
        $this->userRepository = $userRepository;
        $this->industryRepository = $industryRepository;
        $this->positionRepository = $positionRepository;
        $this->licenseTypesRepository = $licenseTypesRepository;
    }

    /**
     * @SWG\Post(
     *     path="/sign-up/user-base",
     *     tags={"ProviderSignUp"},
     *     @SWG\Parameter(name="body", in="body", required=true,
     *     @SWG\Schema(ref="#/definitions/UserBaseRequestProvider")),
     *     @SWG\Response(
     *         response="200",
     *         description="Provider sign-up user base details save",
     *         @SWG\Schema(
     *              @SWG\Property(
     *                  property="code",
     *                  type="string"
     *              ),
     *              @SWG\Property(
     *                  property="user",
     *                  type="object",
     *                  ref="#/definitions/UserBase"
     *              ),
     *         ),
     *     )
     * )
     *
     *
     * @param UserBaseRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function userBaseSave(UserBaseRequest $request)
    {
        try {
            $user = $this->service->userBaseSave($request);
        } catch (\Throwable $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_BAD_GATEWAY);
        }
        return response()->json(['code' => $user->tmp_token, 'user' => $user], Response::HTTP_OK);
    }

    /**
     * @SWG\Post(
     *     path="/sign-up/{code}/additional",
     *     tags={"ProviderSignUp"},
     *     @SWG\Parameter(name="code", in="path", required=true, type="string"),
     *     @SWG\Parameter(name="body", in="body", required=true,
     *     @SWG\Schema(ref="#/definitions/SignupAdditionalRequest")),
     *     @SWG\Response(
     *         response="200",
     *         description="Provider sign-up additional details",
     *         @SWG\Schema(
     *              @SWG\Property(
     *                  property="code",
     *                  type="string"
     *              ),
     *              @SWG\Property(
     *                  property="user",
     *                  type="object",
     *                  ref="#/definitions/UserBase"
     *              ),
     *         ),
     *     )
     * )
     *
     * @param AdditionalRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function additionalSave(AdditionalRequest $request)
    {
        try {
            $user = $this->user;
            $this->service->saveAdditionalUserData($request, $user);
        } catch (\DomainException $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_BAD_GATEWAY);
        }

        return response()->json(['code' => $user->tmp_token, 'user' => $user], Response::HTTP_OK);
    }

    /**
     * @SWG\Post(
     *     path="/sign-up/{code}/industry",
     *     tags={"ProviderSignUp"},
     *     @SWG\Parameter(name="code", in="path", required=true, type="string"),
     *     @SWG\Parameter(name="body", in="body", required=true,
     *     @SWG\Schema(ref="#/definitions/ProviderIndustryRequest")),
     *     @SWG\Response(
     *         response="200",
     *         description="Provider sign-up user industry save",
     *         @SWG\Schema(
     *              @SWG\Property(
     *                  property="code",
     *                  type="string"
     *              ),
     *              @SWG\Property(
     *                  property="user",
     *                  type="object",
     *                  ref="#/definitions/UserProvider"
     *              ),
     *         ),
     *     )
     * )
     *
     *
     * @param IndustryRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function industrySave(IndustryRequest $request)
    {
        try {
            $user = $this->user;
            $this->service->industrySave($request, $user);
        } catch (\DomainException $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_BAD_GATEWAY);
        }

        return response()->json(['code' => $user->tmp_token, 'user' => $user], Response::HTTP_OK);
    }

    /**
     * @SWG\Post(
     *     path="/sign-up/{code}/identity",
     *     tags={"ProviderSignUp"},
     *     @SWG\Parameter(name="code", in="path", required=true, type="string"),
     *     @SWG\Parameter(name="body", in="body", required=true, @SWG\Schema(ref="#/definitions/IdentityRequest")),
     *     @SWG\Response(
     *         response="200",
     *         description="Provider sign-up user identity save",
     *         @SWG\Schema(
     *              @SWG\Property(
     *                  property="code",
     *                  type="string"
     *              ),
     *              @SWG\Property(
     *                  property="user",
     *                  type="object",
     *                  ref="#/definitions/UserProvider"
     *              ),
     *         ),
     *     )
     * )
     *
     *
     * @param IdentityRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function identitySave(IdentityRequest $request)
    {
        try {
            $user = $this->user;
            $this->service->identitySave($request, $user);
        } catch (\DomainException $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_BAD_GATEWAY);
        }

        return response()->json(['code' => $user->tmp_token, 'user' => $user], Response::HTTP_OK);
    }

    /**
     * @SWG\Post(
     *     path="/sign-up/{code}/upload-driver",
     *     tags={"ProviderSignUp"},
     *     @SWG\Parameter(name="code", in="path", required=true, type="string"),
     *     @SWG\Parameter(name="photo", in="formData", required=true, type="file"),
     *     @SWG\Response(
     *         response="200",
     *         description="Provider upload driver license",
     *         @SWG\Schema(
     *              @SWG\Property(
     *                  property="result",
     *                  type="object",
     *                  ref="#/definitions/DriverImageAnalysResult"
     *              ),
     *         ),
     *     )
     * )
     *
     *
     * @param UploadImageRequest $request
     * @return array
     */
    public function uploadDriverLicense(UploadImageRequest $request)
    {
        $user = $this->user;
        $pathes = $this->service->uploadDriverLicense($request, $user);
        $result = $this->service->analyzeImage($pathes, $user);
        return response()->json(['result' => $result], Response::HTTP_OK);
    }

    /**
     * @SWG\Post(
     *     path="/sign-up/{code}/upload-medical",
     *     tags={"ProviderSignUp"},
     *     @SWG\Parameter(name="code", in="path", required=true, type="string"),
     *     @SWG\Parameter(name="photo", in="formData", required=true, type="file"),
     *     @SWG\Parameter(name="position", in="formData", required=true, type="integer"),
     *     @SWG\Response(
     *         response="200",
     *         description="Provider upload medical license photo",
     *         @SWG\Schema(
     *              @SWG\Property(property="path", type="string"),
     *         ),
     *     )
     * )
     *
     *
     * @param UploadImageRequest $request
     * @return string
     */
    public function uploadMedicalLicense(UploadImageRequest $request)
    {
        $user = $this->user;
        $path = $this->service->uploadMedicalLicense($request, $user);
        return response()->json(['path' => $path], Response::HTTP_OK);
    }

    /**
     * @SWG\Post(
     *     path="/sign-up/{code}/license",
     *     tags={"ProviderSignUp"},
     *     @SWG\Parameter(name="code", in="path", required=true, type="string"),
     *     @SWG\Parameter(name="body", in="body", required=true,
     *     @SWG\Schema(ref="#/definitions/MedicalLicenceRequest")),
     *     @SWG\Response(
     *         response="200",
     *         description="Provider sign-up medical licenses save",
     *         @SWG\Schema(
     *              @SWG\Property(
     *                  property="code",
     *                  type="string"
     *              ),
     *              @SWG\Property(
     *                  property="user",
     *                  type="object",
     *                  ref="#/definitions/UserProvider"
     *              ),
     *         ),
     *     )
     * )
     *
     * @param LicenceRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function licenseSave(LicenceRequest $request)
    {
        try {
            $user = $this->user;
            $this->service->licenseSave($request, $user);
        } catch (\DomainException $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_BAD_GATEWAY);
        }

        return response()->json(
            ['code' => $user->tmp_token, 'user' => $this->userRepository->getProviderByTmpCode($request->code)],
            Response::HTTP_OK
        );
    }

    /**
     * @SWG\Delete(
     *     path="/sign-up/{code}/license",
     *     tags={"ProviderSignUp"},
     *     @SWG\Parameter(name="code", in="path", required=true, type="string"),
     *     @SWG\Parameter(name="position", in="formData", required=true, type="integer"),
     *     @SWG\Response(
     *         response="200",
     *         description="Provider sign-up delete medical license by position",
     *         @SWG\Schema(),
     *     )
     * )
     *
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
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
     * @SWG\Post(
     *     path="/sign-up/{code}/check",
     *     tags={"ProviderSignUp"},
     *     @SWG\Parameter(name="code", in="path", required=true, type="string"),
     *     @SWG\Parameter(name="body", in="body", required=true, @SWG\Schema(ref="#/definitions/CheckRequest")),
     *     @SWG\Response(
     *         response="200",
     *         description="Provider sign-up check save",
     *         @SWG\Schema(
     *              @SWG\Property(
     *                  property="user",
     *                  type="object",
     *                  ref="#/definitions/UserProvider"
     *              ),
     *         ),
     *     )
     * )
     *
     *
     * @param CheckRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkSave(CheckRequest $request)
    {
        try {
            $user = $this->user;
            $this->service->checkSave($request, $user);
            //Auth::login($user);
        } catch (\DomainException $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_BAD_GATEWAY);
        }

        return response()->json(['user' => $user], Response::HTTP_OK);
    }
}
