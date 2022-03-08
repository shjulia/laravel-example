<?php

namespace App\Http\Controllers\Api\Data;

use App\Entities\Data\LicenseType;
use App\Entities\Industry\Position;
use App\Entities\User\Role;
use App\Http\Controllers\Controller;
use App\Repositories\Data\HolidaysRepository;
use App\Repositories\Data\LicenseTypesRepository;
use App\Repositories\Data\StatesRepository;
use App\Repositories\User\RolesRepository;
use Illuminate\Http\Response;

/**
 * Class DataController
 * @package App\Http\Controllers\Api\Data
 */
class DataController extends Controller
{
    /**
     * @var StatesRepository
     */
    private $statesRepository;

    /**
     * @var HolidaysRepository
     */
    private $holidaysRepository;

    /**
     * @var RolesRepository
     */
    private $rolesRepository;

    /**
     * @var LicenseTypesRepository
     */
    private $licenseTypesRepository;

    /**
     * DataController constructor.
     * @param StatesRepository $statesRepository
     * @param HolidaysRepository $holidaysRepository
     * @param RolesRepository $rolesRepository
     * @param LicenseTypesRepository $licenseTypesRepository
     */
    public function __construct(
        StatesRepository $statesRepository,
        HolidaysRepository $holidaysRepository,
        RolesRepository $rolesRepository,
        LicenseTypesRepository $licenseTypesRepository
    ) {
        $this->statesRepository = $statesRepository;
        $this->holidaysRepository = $holidaysRepository;
        $this->rolesRepository = $rolesRepository;
        $this->licenseTypesRepository = $licenseTypesRepository;
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     *
     * @SWG\Get(
     *     path="/data/states",
     *     tags={"States"},
     *     @SWG\Response(
     *         response="200",
     *         description="States list",
     *         @SWG\Schema(
     *              @SWG\Property(
     *                  property="states",
     *                  type="array",
     *                      @SWG\Items(
     *                          @SWG\Property(property="id", type="integer"),
     *                          @SWG\Property(property="title", type="string"),
     *                          @SWG\Property(property="short_title", type="string")
     *                      )
     *              )
     *         ),
     *     )
     * )
     */
    public function states()
    {
        $states = $this->statesRepository->getAll();

        return response()->json(['states' => $states], Response::HTTP_OK);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     *
     * @SWG\Get(
     *     path="/data/holidays",
     *     tags={"Holidays"},
     *     @SWG\Response(
     *         response="200",
     *         description="Holidays list",
     *         @SWG\Schema(
     *              @SWG\Property(
     *                  property="holidays",
     *                  type="array",
     *                      @SWG\Items(
     *                          @SWG\Property(property="id", type="integer"),
     *                          @SWG\Property(property="title", type="string")
     *                      )
     *              )
     *         ),
     *     )
     * )
     */
    public function holidays()
    {
        $holidays = $this->holidaysRepository->getAll();

        return response()->json(['holidays' => $holidays], Response::HTTP_OK);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     *
     * @SWG\Get(
     *     path="/data/roles",
     *     tags={"Roles"},
     *     @SWG\Response(
     *         response="200",
     *         description="Roles list",
     *         @SWG\Schema(
     *              @SWG\Property(
     *                  property="roles",
     *                  type="array",
     *                      @SWG\Items(
     *                          @SWG\Property(property="id", type="integer"),
     *                          @SWG\Property(property="title", type="string"),
     *                          @SWG\Property(property="type", type="string")
     *                      )
     *              )
     *         ),
     *     )
     * )
     */
    public function roles()
    {
        $roles = $this->rolesRepository->findAll();

        return response()->json(['roles' => $roles], Response::HTTP_OK);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     *
     * @SWG\Get(
     *     path="/data/practice-roles",
     *     tags={"Practice roles"},
     *     @SWG\Response(
     *         response="200",
     *         description="Practice roles",
     *         @SWG\Schema(
     *              @SWG\Property(
     *                  property="practice_roles",
     *                  type="array",
     *                      @SWG\Items(
     *                          @SWG\Property(property="type", type="string")
     *                      )
     *              )
     *         ),
     *     )
     * )
     */
    public function practiceRoles()
    {
        $roles = Role::practiceRoles();

        return response()->json(['practice_roles' => $roles], Response::HTTP_OK);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     *
     * @SWG\Get(
     *     path="/data/license-types",
     *     tags={"LicenseTypes"},
     *     @SWG\Parameter(name="position", in="path", type="integer"),
     *     @SWG\Response(
     *         response="200",
     *         description="License Types list",
     *         @SWG\Schema(
     *              @SWG\Property(
     *                  property="types",
     *                  type="array",
     *                      @SWG\Items(
     *                          @SWG\Property(property="id", type="integer"),
     *                          @SWG\Property(property="title", type="string")
     *                      )
     *              )
     *         ),
     *     )
     * )
     */
    public function licenseTypes()
    {
        $types = $this->licenseTypesRepository->findAll();
        return response()->json(['types' => $types], Response::HTTP_OK);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     *
     * @SWG\Get(
     *     path="/data/direct-license-types",
     *     tags={"LicenseTypes"},
     *     @SWG\Parameter(name="position", in="path", type="integer"),
     *     @SWG\Parameter(name="state", in="path", type="string", description="State short title"),
     *     @SWG\Response(
     *         response="200",
     *         description="License Types list",
     *         @SWG\Schema(
     *              @SWG\Property(
     *                  property="types",
     *                  type="array",
     *                      @SWG\Items(
     *                          @SWG\Property(
     *                              property="requiredLicense",
     *                              type="array",
     *                              @SWG\Items(
     *                                  @SWG\Property(property="id", type="integer"),
     *                                  @SWG\Property(property="title", type="string"),
     *                                  @SWG\Property(property="required", type="integer", description="(0|1)")
     *                              )
     *                          ),
     *                          @SWG\Property(
     *                              property="anotherLicense",
     *                              type="array",
     *                              @SWG\Items(
     *                                  @SWG\Property(property="id", type="integer"),
     *                                  @SWG\Property(property="title", type="string"),
     *                                  @SWG\Property(property="required", type="integer", description="(0|1)")
     *                              )
     *                          )
     *                      )
     *              )
     *         ),
     *     )
     * )
     */
    public function directLicenseTypes(?Position $position = null, ?string $state = null)
    {
        $types = $this->licenseTypesRepository->findByPositionAndState($position->id, $state);

        return response()->json(['types' => $types], Response::HTTP_OK);
    }
}
