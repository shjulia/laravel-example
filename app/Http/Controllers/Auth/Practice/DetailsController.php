<?php

namespace App\Http\Controllers\Auth\Practice;

use App\Entities\User\Practice\PracticeAddress;
use App\Entities\User\Role;
use App\Entities\User\User;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\Practice\BaseInfoRequest;
use App\Http\Requests\Auth\Practice\Details\BaseDetailsRequest;
use App\Http\Requests\Auth\Practice\Details\SecondaryDetailsRequest;
use App\Http\Requests\Auth\Practice\Details\TeamMemberRequest;
use App\Http\Requests\Auth\Practice\Details\ToolRequest;
use App\Http\Requests\General\PhotoRequest;
use App\Repositories\Data\ToolRepository;
use App\Repositories\User\UserRepository;
use App\UseCases\Auth\Practice\DetailsService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

/**
 * Class DetailsController
 * @package App\Http\Controllers\Auth\Practice
 */
class DetailsController extends Controller
{
    /**
     * @var DetailsService
     */
    private $detailsService;

    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @var User
     */
    private $user;

    /**
     * DetailsController constructor.
     * @param DetailsService $detailsService
     * @param UserRepository $userRepository
     */
    public function __construct(DetailsService $detailsService, UserRepository $userRepository)
    {
        $this->middleware(['auth', 'can:practice-details']);
        $this->middleware(function ($request, $next) {
            try {
                $this->user = $this->userRepository->getUserWithPractice(Auth::user()->id);
                return $next($request);
            } catch (\DomainException $e) {
                return back()->with(['error' => $e->getMessage()]);
            }
        });
        $this->detailsService = $detailsService;
        $this->userRepository = $userRepository;
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
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function baseDetails()
    {
        $user = $this->user;
        return view('register.practice.details.base-details', compact('user'));
    }

    /**
     * @param BaseDetailsRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function saveBaseDetails(BaseDetailsRequest $request)
    {
        $user = $this->user;
        try {
            $this->detailsService->saveDetails($request, $user);
        } catch (\DomainException $e) {
            return back()->with(['error' => $e->getMessage()]);
        }
        return redirect()->route('practice.details.secondary');
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function secondaryDetails()
    {
        $user = $this->user;
        return view('register.practice.details.secondary-details', compact('user'));
    }

    /**
     * @param SecondaryDetailsRequest $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function saveSecondaryDetails(SecondaryDetailsRequest $request)
    {
        $user = $this->user;
        try {
            $this->detailsService->saveSecondaryDetails($request, $user);
        } catch (\DomainException $e) {
            return back()->with(['error' => $e->getMessage()]);
        }
        return redirect()->route('practice.details.tool');
    }

    /**
     * @param ToolRepository $toolRepository
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function tool(ToolRepository $toolRepository)
    {
        $user = $this->user;
        $tools = $toolRepository->findAll();
        return view('register.practice.details.tool', compact('user', 'tools'));
    }

    /**
     * @param SecondaryDetailsRequest $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function saveTool(ToolRequest $request)
    {
        $user = $this->user;
        try {
            $this->detailsService->saveTool($request, $user);
        } catch (\DomainException $e) {
            return back()->with(['error' => $e->getMessage()]);
        }
        return redirect()->route('practice.details.locations');
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function team()
    {
        $user = $this->user;
        $users = $user->practice->users;
        $roles = Role::practiceRoles();
        return view('register.practice.details.team', compact('user', 'users', 'roles'));
    }

    /**
     * @param TeamMemberRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function teamSave(TeamMemberRequest $request)
    {
        $user = $this->user;
        try {
            $user = $this->detailsService->saveTeamMember($request, $user);
        } catch (\DomainException $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_BAD_GATEWAY);
        }
        return response()->json(['user' => $user], Response::HTTP_OK);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteMember(Request $request)
    {
        $user = $this->user;
        try {
            $this->detailsService->deleteTeamMember($request->member, $user);
        } catch (\DomainException $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_BAD_GATEWAY);
        }
        return response()->json([], Response::HTTP_OK);
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function locations()
    {
        $user = $this->user;
        $practice = $user->practice;
        $locations = $practice->addresses;
        return view('register.practice.details.locations', compact('practice', 'locations'));
    }

    /**
     * @param BaseInfoRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function addLocation(BaseInfoRequest $request)
    {
        $user = $this->user;
        try {
            $this->detailsService->addLocation($request, $user);
        } catch (\DomainException $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_BAD_GATEWAY);
        }
        return response()->json([], Response::HTTP_OK);
    }

    /**
     * @param BaseInfoRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function editCurrentLocation(BaseInfoRequest $request)
    {
        $user = $this->user;
        try {
            $this->detailsService->editCurrentLocation($request, $user);
        } catch (\DomainException $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_BAD_GATEWAY);
        }
        return response()->json([], Response::HTTP_OK);
    }

    /**
     * @param BaseInfoRequest $request
     * @param PracticeAddress $practiceAddress
     * @return \Illuminate\Http\JsonResponse
     */
    public function editLocation(BaseInfoRequest $request, PracticeAddress $practiceAddress)
    {
        $user = $this->user;
        try {
            $this->detailsService->editLocation($request, $practiceAddress, $user);
        } catch (\DomainException $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_BAD_GATEWAY);
        }
        return response()->json([], Response::HTTP_OK);
    }

    /**
     * @param PracticeAddress $practiceAddress
     * @return \Illuminate\Http\RedirectResponse
     */
    public function removeLocation(PracticeAddress $practiceAddress)
    {
        $user = $this->user;
        try {
            $this->detailsService->removeLocation($practiceAddress, $user);
        } catch (\DomainException $e) {
            return back()->with(['error' => $e->getMessage()]);
        }
        return back()->with(['success' => 'Location removed successfully.']);
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function billing()
    {
        $user = $this->user;
        return view('register.practice.details.billing', compact('user'));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function billingSave(Request $request)
    {
        $user = $this->user;
        try {
            $this->detailsService->billingSave($request, $user);
        } catch (\DomainException $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_BAD_GATEWAY);
        }
        return response()->json(['route' => route('practice.details.success')], Response::HTTP_OK);
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function success()
    {
        $user = $this->user;
        return view('register.practice.details.success', compact('user'));
    }
}
