<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\Users\Provider;

use App\Entities\User\User;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\User\Edit\DetailsRequest;
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
 * Class EditDetailsController
 * @package App\Http\Controllers\Admin\Users\Provider
 */
class EditDetailsController extends Controller
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
     * @var AutocompletePlaceService
     */
    private $autocompletePlaceService;

    /**
     * @var TaskRepository
     */
    private $taskRepository;

    /**
     * DetailsController constructor.
     * @param SpecialistRepository $specialistRepository
     * @param SpecialityRepository $specialityRepository
     * @param HolidaysRepository $holidaysRepository
     * @param DetailsService $detailsService
     * @param AutocompletePlaceService $autocompletePlaceService
     * @param TaskRepository $taskRepository
     */
    public function __construct(
        SpecialistRepository $specialistRepository,
        SpecialityRepository $specialityRepository,
        HolidaysRepository $holidaysRepository,
        DetailsService $detailsService,
        AutoCompletePlaceService $autocompletePlaceService,
        TaskRepository $taskRepository
    ) {
        $this->specialistRepository = $specialistRepository;
        $this->specialityRepository = $specialityRepository;
        $this->holidayRepository = $holidaysRepository;
        $this->detailsService = $detailsService;
        $this->autocompletePlaceService = $autocompletePlaceService;
        $this->taskRepository = $taskRepository;
        $this->middleware(function ($request, $next) {
            $this->detailsService->setAdmin(Auth::user());
            return $next($request);
        });
    }

    /**
     * @param User $user
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showForm(User $user)
    {
        $funding_sources = $user->fundingSources()->get();
        $specialist = $this->specialistRepository->getAdditionalDataByUser($user);
        $specialities = $this->specialityRepository->findAllByUser($user);
        $holidays = $this->holidayRepository->getAll();
        $routine_tasks = $this->taskRepository->getByPosition($user->specialist->position_id);
        $specialist_routine_tasks = $user->specialist->routineTasks->pluck('id')->toArray();
        $tab = "details";

        return view(
            'admin.users.edit.provider.details',
            compact(
                'specialist',
                'specialities',
                'holidays',
                'funding_sources',
                'routine_tasks',
                'specialist_routine_tasks',
                'tab',
                'user'
            )
        );
    }

    /**
     * @param PhotoRequest $request
     * @param User $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function savePhoto(PhotoRequest $request, User $user)
    {
        try {
            $path = $this->detailsService->savePhoto($request, $user);
            return response()->json([$path], Response::HTTP_OK);
        } catch (\DomainException $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_BAD_GATEWAY);
        }
    }

    /**
     * @param DetailsRequest $request
     * @param User $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(DetailsRequest $request, User $user)
    {
        try {
            $this->detailsService->saveDetails($request, $user);
            return back()->with(['success' => 'Information updated successfully']);
        } catch (\DomainException $e) {
            return back()->with(['error' => $e->getMessage()]);
        } catch (\Exception $e) {
            return back()->with(['error' => $e->getMessage()]);
        }
    }

    /**
     * @param User $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function changeAvailability(User $user)
    {
        try {
            $this->detailsService->changeAvailability($user->specialist);
            return back()->with(['success' => 'Information updated successfully']);
        } catch (\DomainException $e) {
            return back()->with(['error' => $e->getMessage()]);
        }
    }
}
