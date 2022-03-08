<?php

namespace App\Http\Controllers\Api\Auth\Practice;

use App\Entities\User\User;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\Practice\BaseInfoRequest;
use App\Http\Requests\Auth\Practice\IndustryRequest;
use App\Http\Requests\Auth\Practice\InsuranceRequest;
use App\Http\Requests\Auth\Practice\UploadImageOrPDFRequest;
use App\Http\Requests\Auth\Practice\UserBaseRequest;
use App\Http\Requests\Auth\Provider\UploadImageRequest;
use App\Repositories\Industry\IndustryRepository;
use App\Repositories\User\UserRepository;
use App\UseCases\Auth\Practice\RegisterService;
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
        $this->middleware('guest');
        $this->middleware(function ($request, $next) {
            try {
                $this->user = $this->userRepository->getPracticeByTmpCode($request->code);
                return $next($request);
            } catch (\DomainException $e) {
                return response()->json(['error' => $e->getMessage()], Response::HTTP_BAD_GATEWAY);
            }
        })->except(['userBaseSave', 'autocomplete', 'placeData']);
        $this->service = $service;
        $this->userRepository = $userRepository;
        $this->industryRepository = $industryRepository;
    }

    /**
     * @SWG\Post(
     *     path="/practice/sign-up/user-base",
     *     tags={"PracticeSignUp"},
     *     @SWG\Parameter(name="body", in="body", required=true, @SWG\Schema(ref="#/definitions/UserBaseRequest")),
     *     @SWG\Response(
     *         response="200",
     *         description="Practice sign-up user base details save",
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
            $user = $this->service->saveUserBase($request);
        } catch (\Throwable $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_BAD_GATEWAY);
        }
        return response()->json(['code' => $user->tmp_token, 'user' => $user], Response::HTTP_OK);
    }

    /**
     * @SWG\Post(
     *     path="/practice/sign-up/{code}/industry",
     *     tags={"PracticeSignUp"},
     *     @SWG\Parameter(name="code", in="path", required=true, type="string"),
     *     @SWG\Parameter(
     *          name="body", in="body", required=true, @SWG\Schema(ref="#/definitions/PracticeIndustryRequest")
     *      ),
     *     @SWG\Response(
     *         response="200",
     *         description="Practice sign-up user industry save",
     *         @SWG\Schema(
     *              @SWG\Property(
     *                  property="code",
     *                  type="string"
     *              ),
     *              @SWG\Property(
     *                  property="user",
     *                  type="object",
     *                  ref="#/definitions/UserPractice"
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
            $this->service->saveIndustry($request, $user);
        } catch (\DomainException $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_BAD_GATEWAY);
        }
        return response()->json(['code' => $user->tmp_token, 'user' => $user], Response::HTTP_OK);
    }

    /**
     * @SWG\Post(
     *     path="/practice/sign-up/{code}/base",
     *     tags={"PracticeSignUp"},
     *     @SWG\Parameter(name="code", in="path", required=true, type="string"),
     *     @SWG\Parameter(
     *     name="body", in="body", required=true, @SWG\Schema(ref="#/definitions/PracticeBaseInfoRequest")
     * ),
     *     @SWG\Response(
     *         response="200",
     *         description="Practice sign-up user practice base save",
     *         @SWG\Schema(
     *              @SWG\Property(
     *                  property="code",
     *                  type="string"
     *              ),
     *              @SWG\Property(
     *                  property="user",
     *                  type="object",
     *                  ref="#/definitions/UserPractice"
     *              ),
     *         ),
     *     )
     * )
     *
     *
     * @param BaseInfoRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function baseSave(BaseInfoRequest $request)
    {
        try {
            $user = $this->user;
            $this->service->saveBaseInfo($request, $user);
        } catch (\DomainException $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_BAD_GATEWAY);
        }

        return response()->json(['code' => $user->tmp_token, 'user' => $user], Response::HTTP_OK);
    }

    /**
     *     @SWG\Get(
     *     path="/practice/sign-up/autocomplete/{query}/{lat}/{lng}",
     *     tags={"PracticeSignUp"},
     *     @SWG\Parameter(name="query", in="path", required=true, type="string"),
     *     @SWG\Parameter(name="lat", in="path", required=false, type="string"),
     *     @SWG\Parameter(name="lng", in="path", required=false, type="string"),
     *     @SWG\Response(
     *         response="200",
     *         description="Practice sign-up practice name autocmplete",
     *     )
     * )
     * @param string $query
     * @param null|string $lat
     * @param null|string $lng
     * @return \Illuminate\Http\JsonResponse
     */
    public function autocomplete(string $query, ?string $lat = null, ?string $lng = null)
    {
        $res = $this->service->autocompleteQuery($query, $lat, $lng, true);
        return response()->json($res, Response::HTTP_OK);
    }

    /**
     *     @SWG\Get(
     *     path="/practice/sign-up/place-data/{placeId}",
     *     tags={"PracticeSignUp"},
     *     @SWG\Parameter(name="placeId", in="path", required=false, type="string"),
     *     @SWG\Response(
     *         response="200",
     *         description="Practice sign-up practice place data",
     *     )
     * )
     * @param string $placeId
     * @return \Illuminate\Http\JsonResponse
     */
    public function placeData(string $placeId)
    {
        $res = $this->service->getPlaceData($placeId);
        return response()->json($res, Response::HTTP_OK);
    }

    /**
     * @SWG\Post(
     *     path="/practice/sign-up/{code}/upload-insurance",
     *     tags={"PracticeSignUp"},
     *     @SWG\Parameter(name="code", in="path", required=true, type="string"),
     *     @SWG\Parameter(name="photo", in="formData", required=true, type="file"),
     *     @SWG\Response(
     *         response="200",
     *         description="Practice sign-up user practice upload insurance",
     *         @SWG\Schema(
     *              @SWG\Property(
     *                  property="url",
     *                  type="string"
     *              )
     *         ),
     *     )
     * )
     *
     *
     * @param UploadImageRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function uploadInsurance(UploadImageOrPDFRequest $request)
    {
        $user = $this->user;
        $path = $this->service->uploadPolicyPhoto($request, $user);
        return response()->json(['path' => $path], Response::HTTP_OK);
    }

    /**
     * @SWG\Post(
     *     path="/practice/sign-up/{code}/insurance",
     *     tags={"PracticeSignUp"},
     *     @SWG\Parameter(name="code", in="path", required=true, type="string"),
     *     @SWG\Parameter(
     *     name="body", in="body", required=true, @SWG\Schema(ref="#/definitions/PracticeInsuranceRequest")
     * ),
     *     @SWG\Response(
     *         response="200",
     *         description="Practice sign-up user practice insurance save",
     *         @SWG\Schema(
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              )
     *         ),
     *     )
     * )
     *
     *
     * @param InsuranceRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function insuranceSave(InsuranceRequest $request)
    {
        try {
            $user = $this->user;
            $this->service->saveInsurance($request, $user);
            //Auth::login($user); TODO set access token to user
        } catch (\DomainException $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_BAD_GATEWAY);
        }

        return response()->json(['success' => true], Response::HTTP_OK);
    }
}
