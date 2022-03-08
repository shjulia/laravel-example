<?php

namespace App\Http\Controllers\Api\Data;

use App\Entities\Industry\Industry;
use App\Http\Controllers\Controller;
use App\Repositories\Industry\IndustryRepository;
use App\Repositories\Industry\PositionRepository;
use App\Repositories\Industry\SpecialityRepository;
use Illuminate\Http\Response;

/**
 * Class IndustryController
 * @package App\Http\Controllers\Api\Data
 */
class IndustryController extends Controller
{
    /**
     * @var IndustryRepository
     */
    private $industryRepository;

    /**
     * @var PositionRepository
     */
    private $positionRepository;

    /**
     * @var SpecialityRepository
     */
    private $specialityRepository;

    /**
     * IndustryController constructor.
     * @param IndustryRepository $industryRepository
     * @param PositionRepository $positionRepository
     * @param SpecialityRepository $specialityRepository
     */
    public function __construct(
        IndustryRepository $industryRepository,
        PositionRepository $positionRepository,
        SpecialityRepository $specialityRepository
    ) {
        $this->industryRepository = $industryRepository;
        $this->positionRepository = $positionRepository;
        $this->specialityRepository = $specialityRepository;
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     *
     * @SWG\Get(
     *     path="/data/industries",
     *     tags={"Industries"},
     *     @SWG\Response(
     *         response="200",
     *         description="Industries list",
     *         @SWG\Schema(
     *              @SWG\Property(
     *                  property="industries",
     *                  type="array",
     *                      @SWG\Items(
     *                          @SWG\Property(property="id", type="integer"),
     *                          @SWG\Property(property="title", type="string"),
     *                          @SWG\Property(property="alias", type="string")
     *                      )
     *              )
     *         ),
     *     ),
     *     security={{"Bearer": {}, "OAuth2": {}}}
     * )
     */
    public function industries()
    {
        $industries = $this->industryRepository->getAll();
        return response()->json(['industries' => $industries], Response::HTTP_OK);
    }

    /**
     * @param Industry $industry
     * @return \Illuminate\Http\JsonResponse
     *
     * @SWG\Get(
     *     path="/data/positions/{industry}",
     *     tags={"Positions"},
     *     @SWG\Parameter(name="industry", in="path", required=true, type="integer"),
     *     @SWG\Response(
     *         response="200",
     *         description="Positions by industry list",
     *         @SWG\Schema(
     *              @SWG\Property(
     *                  property="positions",
     *                  type="array",
     *                      @SWG\Items(
     *                          @SWG\Property(property="id", type="integer"),
     *                          @SWG\Property(property="title", type="string"),
     *                          @SWG\Property(property="industry_id", type="integer"),
     *                      )
     *              )
     *         ),
     *     )
     * )
     */
    public function positions(Industry $industry)
    {
        $positions = $this->positionRepository->getByIndustry($industry);
        return response()->json(['positions' => $positions], Response::HTTP_OK);
    }

    /**
     * @param Industry $industry
     * @return \Illuminate\Http\JsonResponse
     *
     * @SWG\Get(
     *     path="/data/specialities/{industry}",
     *     tags={"Specialities"},
     *     @SWG\Parameter(name="industry", in="path", required=true, type="integer"),
     *     @SWG\Response(
     *         response="200",
     *         description="Specialities by industry list",
     *         @SWG\Schema(
     *              @SWG\Property(
     *                  property="specialities",
     *                  type="array",
     *                      @SWG\Items(
     *                          @SWG\Property(property="id", type="integer"),
     *                          @SWG\Property(property="title", type="string"),
     *                          @SWG\Property(property="industry_id", type="integer"),
     *                      )
     *              )
     *         ),
     *     )
     * )
     */
    public function specialities(Industry $industry)
    {
        $specialities = $this->specialityRepository->findByIndustry($industry);
        return response()->json(['specialities' => $specialities], Response::HTTP_OK);
    }
}
