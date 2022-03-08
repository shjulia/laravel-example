<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth\Provider;

use App\Entities\User\User;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\Practice\Details\SecondaryDetailsRequest;
use App\Http\Requests\Auth\Practice\Details\ToolRequest;
use App\Http\Requests\Auth\Provider\Onboarding\AvailabilityRequest;
use App\Http\Requests\Auth\Provider\Onboarding\DistanceRequest;
use App\Http\Requests\Auth\Provider\Onboarding\HolidaysRequest;
use App\Http\Requests\Auth\Provider\Onboarding\LengthRequest;
use App\Http\Requests\Auth\Provider\Onboarding\PhotoNextRequest;
use App\Http\Requests\Auth\Provider\Onboarding\RateRequest;
use App\Http\Requests\Auth\Provider\Onboarding\SpecialitiesRequest;
use App\Http\Requests\Auth\Provider\Onboarding\TasksRequest;
use App\Repositories\Data\HolidaysRepository;
use App\Repositories\Data\ToolRepository;
use App\Repositories\Industry\SpecialityRepository;
use App\Repositories\Industry\TaskRepository;
use App\UseCases\Auth\Provider\DetailsService;
use App\UseCases\Auth\Provider\OnboardingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Class OnboardingController
 * @package App\Http\Controllers\Auth\Provider
 */
class OnboardingController extends Controller
{
    /**
     * @var OnboardingService
     */
    private $onboardingService;

    /**
     * OnboardingController constructor.
     * @param OnboardingService $onboardingService
     */
    public function __construct(OnboardingService $onboardingService)
    {
        $this->onboardingService = $onboardingService;
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function photo()
    {
        /** @var User $user */
        $user = Auth::user();
        return view('register.provider.onboarding.photo', compact('user'));
    }

    /**
     * @param PhotoNextRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function photoNext(PhotoNextRequest $request)
    {
        return redirect()->route('provider.onboarding.shiftLength');
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function shiftLength()
    {
        /** @var User $user */
        $user = Auth::user();
        return view('register.provider.onboarding.shift-length', compact('user'));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function shiftLengthSave(LengthRequest $request)
    {
        /** @var User $user */
        $user = Auth::user();
        try {
            $this->onboardingService->setShiftLength($user, $request);
        } catch (\DomainException $e) {
            return back()->with(['error' => $e->getMessage()]);
        }
        return redirect()->route('provider.onboarding.distance');
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function distance()
    {
        /** @var User $user */
        $user = Auth::user();
        return view('register.provider.onboarding.distance', compact('user'));
    }

    /**
     * @param DistanceRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function distanceSave(DistanceRequest $request)
    {
        /** @var User $user */
        $user = Auth::user();
        try {
            $this->onboardingService->setDistance($user, $request);
        } catch (\DomainException $e) {
            return back()->with(['error' => $e->getMessage()]);
        }
        return redirect()->route('provider.onboarding.rate');
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function rate()
    {
        /** @var User $user */
        $user = Auth::user();
        $positionRate = $user->specialist->position->fee;
        return view('register.provider.onboarding.rate', compact('user', 'positionRate'));
    }

    /**
     * @param RateRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function rateSave(RateRequest $request)
    {
        /** @var User $user */
        $user = Auth::user();
        try {
            $this->onboardingService->setRate($user, (float)$request->rate);
        } catch (\DomainException $e) {
            return back()->with(['error' => $e->getMessage()]);
        }
        return redirect()->route('provider.onboarding.tool');
    }

    /**
     * @param ToolRepository $toolRepository
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function tool(ToolRepository $toolRepository)
    {
        /** @var User $user */
        $user = Auth::user();
        $tools = $toolRepository->findAll();
        return view('register.provider.onboarding.tool', compact('user', 'tools'));
    }

    /**
     * @param ToolRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function saveTool(ToolRequest $request)
    {
        /** @var User $user */
        $user = Auth::user();
        try {
            $this->onboardingService->saveTool($request, $user);
        } catch (\DomainException $e) {
            return back()->with(['error' => $e->getMessage()]);
        }
        return redirect()->route('provider.onboarding.tasks');
    }

    /**
     * @param TaskRepository $taskRepository
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function tasks(TaskRepository $taskRepository)
    {
        /** @var User $user */
        $user = Auth::user();
        $routineTasks = $taskRepository->getByPosition($user->specialist->position_id);
        $specialistTasks = $user->specialist->routineTasks->pluck('id')->toArray();
        return view('register.provider.onboarding.tasks', compact('user', 'routineTasks', 'specialistTasks'));
    }

    /**
     * @param TasksRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function tasksSave(TasksRequest $request)
    {
        /** @var User $user */
        $user = Auth::user();
        try {
            $this->onboardingService->setRoutineTasks($user, $request);
        } catch (\DomainException $e) {
            return back()->with(['error' => $e->getMessage()]);
        }
        return redirect()->route('provider.onboarding.specialities');
    }

    /**
     * @param SpecialityRepository $specialityRepository
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function specialities(SpecialityRepository $specialityRepository)
    {
        /** @var User $user */
        $user = Auth::user();
        $specialities = $specialityRepository->findAllByUser($user);
        $specialistSpecialities = $user->specialist->specialities->pluck('id')->toArray();
        return view(
            'register.provider.onboarding.specialities',
            compact('user', 'specialities', 'specialistSpecialities')
        );
    }

    /**
     * @param SpecialitiesRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function specialitiesSave(SpecialitiesRequest $request)
    {
        /** @var User $user */
        $user = Auth::user();
        try {
            $this->onboardingService->setSpecialities($user, $request);
        } catch (\DomainException $e) {
            return back()->with(['error' => $e->getMessage()]);
        }
        return redirect()->route('provider.onboarding.availability');
    }

    /**
     * @param HolidaysRepository $holidaysRepository
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function holidays(HolidaysRepository $holidaysRepository)
    {
        /** @var User $user */
        $user = Auth::user();
        $holidays = $holidaysRepository->getAll();
        return view('register.provider.onboarding.holidays', compact('user', 'holidays'));
    }

    /**
     * @param SpecialitiesRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function holidaysAvailabilitySave(HolidaysRequest $request)
    {
        /** @var User $user */
        $user = Auth::user();
        try {
            $this->onboardingService->setHolidaysAvailability($user, $request);
        } catch (\DomainException $e) {
            return back()->with(['error' => $e->getMessage()]);
        }
        return redirect()->route('home');
    }

    public function availability(HolidaysRepository $holidaysRepository)
    {
        /** @var User $user */
        $user = Auth::user();
        $specialist = $user->specialist;
        return view('register.provider.onboarding.availability', compact('user', 'specialist'));
    }

    /**
     * @param AvailabilityRequest $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function availabilitySave(AvailabilityRequest $request)
    {
        /** @var User $user */
        $user = Auth::user();
        try {
            $this->onboardingService->setAvailability($user, $request);
        } catch (\DomainException $e) {
            return back()->with(['error' => $e->getMessage()]);
        }
        return redirect()->route('provider.onboarding.holidays');
    }
}
