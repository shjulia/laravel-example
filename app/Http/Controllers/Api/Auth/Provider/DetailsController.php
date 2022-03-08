<?php

namespace App\Http\Controllers\Api\Auth\Provider;

use App\Entities\User\User;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\Provider\DetailsRequest;
use App\Http\Requests\General\PhotoRequest;
use App\Repositories\Data\HolidaysRepository;
use App\Repositories\Industry\SpecialityRepository;
use App\Repositories\User\SpecialistRepository;
use App\UseCases\Auth\Provider\DetailsService;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

/**
 * Class DetailsController
 * @package App\Http\Controllers\Api\Auth\Provider
 */
class DetailsController extends Controller
{
    /**
     * @var SpecialistRepository
     */
    private $specialistRepository;

    /**
     * @var SpecialityRepository
     */
    private $specialityRepository;

    /**
     * @var HolidaysRepository
     */
    private $holidayRepository;

    /**
     * @var DetailsService
     */
    private $detailsService;

    /**
     * DetailsController constructor.
     * @param SpecialistRepository $specialistRepository
     * @param SpecialityRepository $specialityRepository
     * @param HolidaysRepository $holidaysRepository
     * @param DetailsService $detailsService
     */
    public function __construct(
        SpecialistRepository $specialistRepository,
        SpecialityRepository $specialityRepository,
        HolidaysRepository $holidaysRepository,
        DetailsService $detailsService
    ) {
        $this->middleware(['auth:api', 'can:provider-account-details']);
        $this->specialistRepository = $specialistRepository;
        $this->specialityRepository = $specialityRepository;
        $this->holidayRepository = $holidaysRepository;
        $this->detailsService = $detailsService;
    }

    /**
     * @SWG\Post(
     *     path="/save-photo",
     *     tags={"ProviderDetails"},
     *     @SWG\Parameter(name="file", in="formData", required=true, type="file"),
     *     @SWG\Response(
     *         response="200",
     *         description="Provider upload driver license",
     *         @SWG\Schema(
     *              @SWG\Property(property="path", type="string"),
     *         ),
     *     ),
     *     security={{"Bearer": {}, "OAuth2": {}}}
     * )
     *
     *
     * @param PhotoRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function savePhoto(PhotoRequest $request)
    {
        /** @var User $user */
        $user = Auth::user();
        try {
            $path = $this->detailsService->savePhoto($request, $user);
            return response()->json([$path], Response::HTTP_OK);
        } catch (\DomainException $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_BAD_GATEWAY);
        }
    }

    /**
     * @SWG\Get(
     *     path="/account-detail",
     *     tags={"ProviderDetails"},
     *     @SWG\Response(
     *         response="200",
     *         description="Data for details",
     *         @SWG\Schema(
     *              @SWG\Property(
     *                  property="user",
     *                  type="object",
     *                  ref="#/definitions/UserProvider"
     *              ),
     *              @SWG\Property(
     *                  property="specialities",
     *                  type="array",
     *                      @SWG\Items(
     *                          @SWG\Property(property="id", type="integer"),
     *                          @SWG\Property(property="title", type="string"),
     *                          @SWG\Property(property="industry_id", type="string"),
     *                      )
     *              ),
     *              @SWG\Property(
     *                  property="holidays",
     *                  type="array",
     *                      @SWG\Items(
     *                          @SWG\Property(property="id", type="integer"),
     *                          @SWG\Property(property="title", type="string")
     *                      )
     *              )
     *         ),
     *     ),
     *     security={{"Bearer": {}, "OAuth2": {}}}
     * )
     *
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function showForm()
    {
        /** @var User $user */
        $user = Auth::user();
        $specialist = $this->specialistRepository->getAdditionalDataByUser($user);
        $specialities = $this->specialityRepository->findAllByUser($user);
        $holidays = $this->holidayRepository->getAll();
        return response()->json(compact('specialist', 'specialities', 'holidays'), Response::HTTP_OK);
    }

    /**
     * @SWG\Post(
     *     path="/save-details",
     *     tags={"ProviderDetails"},
     *     @SWG\Parameter(name="body", in="body", required=true,
     *     @SWG\Schema(ref="#/definitions/ProviderDetailsRequest")),
     *     @SWG\Response(
     *         response="200",
     *         description="Provider acc details",
     *         @SWG\Schema(
     *              @SWG\Property(
     *                  property="success",
     *                  type="string"
     *              )
     *         ),
     *     ),
     *     security={{"Bearer": {}, "OAuth2": {}}}
     * )
     *
     *
     * @param DetailsRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(DetailsRequest $request)
    {
        /** @var User $user */
        $user = Auth::user();
        try {
            $this->detailsService->saveDetails($request, $user);
            return response()->json(['success' => 'Information updated successfully'], Response::HTTP_OK);
        } catch (\DomainException $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_BAD_GATEWAY);
        }
    }
}
