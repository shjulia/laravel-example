<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\Users\Practice;

use App\Entities\User\Role;
use App\Entities\User\User;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\User\Edit\BaseDetailsRequest;
use App\Http\Requests\Auth\Practice\Details\SecondaryDetailsRequest;
use App\Http\Requests\Auth\Practice\Details\TeamMemberRequest;
use App\Http\Requests\General\PhotoRequest;
use App\Repositories\User\UserRepository;
use App\UseCases\Auth\Practice\DetailsService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

/**
 * Class EditDetailsController
 * @package App\Http\Controllers\Admin\Users\Practice
 */
class EditDetailsController extends Controller
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
     * EditDetailsController constructor.
     * @param DetailsService $detailsService
     * @param UserRepository $userRepository
     */
    public function __construct(DetailsService $detailsService, UserRepository $userRepository)
    {
        $this->detailsService = $detailsService;
        $this->middleware(function ($request, $next) {
            $this->detailsService->setAdmin(Auth::user());
            return $next($request);
        });
        $this->userRepository = $userRepository;
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
     * @param User $user
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function baseDetails(User $user)
    {
        $tab = "base-details";
        return view('admin.users.edit.practice.details.base-details', compact('user', 'tab'));
    }

    /**
     * @param BaseDetailsRequest $request
     * @param User $user
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function saveBaseDetails(BaseDetailsRequest $request, User $user)
    {
        try {
            $this->detailsService->saveDetails($request, $user);
        } catch (\DomainException $e) {
            return back()->with(['error' => $e->getMessage()]);
        }
        return back()->with(['success' => 'Practice have been updated successfully']);
    }

    /**
     * @param User $user
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function secondaryDetails(User $user)
    {
        $tab = "secondary-details";
        return view('admin.users.edit.practice.details.secondary-details', compact('user', 'tab'));
    }

    /**
     * @param SecondaryDetailsRequest $request
     * @param User $user
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function saveSecondaryDetails(SecondaryDetailsRequest $request, User $user)
    {
        try {
            $this->detailsService->saveSecondaryDetails($request, $user);
        } catch (\DomainException $e) {
            return back()->with(['error' => $e->getMessage()]);
        }
        return back()->with(['success' => 'Practice have been updated successfully']);
    }

    /**
     * @param User $user
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function team(User $user)
    {
        $tab = "team";
        $users = $user->practice->users;
        $roles = Role::practiceRoles();
        return view('admin.users.edit.practice.details.team', compact('user', 'users', 'roles', 'tab'));
    }

    /**
     * @param TeamMemberRequest $request
     * @param User $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function teamSave(TeamMemberRequest $request, User $user)
    {
        try {
            $user = $this->detailsService->saveTeamMember($request, $user);
        } catch (\DomainException $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_BAD_GATEWAY);
        }
        return response()->json(['user' => $user], Response::HTTP_OK);
    }

    /**
     * @param Request $request
     * @param User $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteMember(Request $request, User $user)
    {
        try {
            $this->detailsService->deleteTeamMember($request->member, $user);
        } catch (\DomainException $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_BAD_GATEWAY);
        }
        return response()->json([], Response::HTTP_OK);
    }
}
