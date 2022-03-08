<?php

namespace App\Http\Controllers\Auth\Provider;

use App\Entities\User\User;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\Provider\DetailsRequest;
use App\Http\Requests\General\PhotoRequest;
use App\Repositories\Data\HolidaysRepository;
use App\Repositories\Industry\SpecialityRepository;
use App\Repositories\Industry\TaskRepository;
use App\Repositories\User\SpecialistRepository;
use App\Services\Maps\AutocompletePlaceService;
use App\UseCases\Auth\Provider\DetailsService;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

/**
 * Class DetailsController
 * @package App\Http\Controllers\Auth\Provider
 */
class DetailsController extends Controller
{
    /**
     * @var DetailsService
     */
    private $detailsService;

    /**
     * @var AutocompletePlaceService
     */
    private $autocompletePlaceService;

    /**
     * DetailsController constructor.
     * @param DetailsService $detailsService
     * @param AutocompletePlaceService $autocompletePlaceService
     */
    public function __construct(
        DetailsService $detailsService,
        AutoCompletePlaceService $autocompletePlaceService
    ) {
        $this->middleware(['auth', 'can:provider-account-details']);
        $this->detailsService = $detailsService;
        $this->autocompletePlaceService = $autocompletePlaceService;
    }

    /**
     * @param SpecialistRepository $specialistRepository
     * @param SpecialityRepository $specialityRepository
     * @param HolidaysRepository $holidaysRepository
     * @param TaskRepository $taskRepository
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showForm(
        SpecialistRepository $specialistRepository,
        SpecialityRepository $specialityRepository,
        HolidaysRepository $holidaysRepository,
        TaskRepository $taskRepository
    ) {
        /** @var User $user */
        $user = Auth::user();
        $funding_sources = $user->fundingSources()->get();
        $specialist = $specialistRepository->getAdditionalDataByUser($user);
        $specialities = $specialityRepository->findAllByUser($user);
        $holidays = $holidaysRepository->getAll();
        $routine_tasks = $taskRepository->getByPosition($user->specialist->position_id);
        $specialist_routine_tasks = $user->specialist->routineTasks->pluck('id')->toArray();

        return view(
            'register.provider.details.show',
            compact(
                'specialist',
                'specialities',
                'holidays',
                'funding_sources',
                'routine_tasks',
                'specialist_routine_tasks',
                'user'
            )
        );
    }

    /**
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
     * @param DetailsRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(DetailsRequest $request)
    {
        $user = Auth::user();
        try {
            $this->detailsService->saveDetails($request, $user);
            return redirect()->route('dashboard');
        } catch (\DomainException $e) {
            return back()->with(['error' => $e->getMessage()]);
        } catch (\Exception $e) {
            return back()->with(['error' => $e->getMessage()]);
        }
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
