<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\Users;

use App\Entities\Invite\Invite;
use App\Entities\Payment\ProviderCharge;
use App\Entities\User\User;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\User\Edit\InviteEditRequest;
use App\Http\Requests\Admin\User\Edit\UserDataRequest;
use App\Http\Requests\Admin\User\UpdateRequest;
use App\Repositories\User\UserRepository;
use App\UseCases\Admin\Manage\Users\EditService;
use Illuminate\Support\Facades\Auth;

/**
 * Class EditController
 * @package App\Http\Controllers\Admin\Users
 */
class EditController extends Controller
{
    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @var EditService
     */
    private $usersService;

    /**
     * EditController constructor.
     * @param UserRepository $userRepository
     * @param EditService $usersService
     */
    public function __construct(
        UserRepository $userRepository,
        EditService $usersService
    ) {
        $this->userRepository = $userRepository;
        $this->usersService = $usersService;
    }

    /**
     * @param User $user
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function userData(User $user)
    {
        $tab = "userData";
        return view('admin.users.edit.user-data', compact('user', 'tab'));
    }

    /**
     * @param UpdateRequest $request
     * @param User $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function userDataEdit(UserDataRequest $request, User $user)
    {
        $admin = Auth::user();
        try {
            $this->usersService->editUserData($request, $user, $admin);
        } catch (\DomainException $e) {
            return back()->with(['error' => $e->getMessage()]);
        }
        return back()->with(['success' => 'User info updated successfully']);
    }

    /**
     * @param Invite $invite
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function editInvite(Invite $invite)
    {
        $systems = ProviderCharge::paymentSystemLists();
        $statuses = Invite::statusesLists();
        return view('admin.users.edit.referral-charge', compact('invite', 'systems', 'statuses'));
    }

    /**
     * @param InviteEditRequest $request
     * @param Invite $invite
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateInvite(InviteEditRequest $request, Invite $invite)
    {
        try {
            $this->usersService->editInvite($invite, $request);
        } catch (\DomainException $e) {
            return redirect()->back()->with(['error' => $e->getMessage()]);
        }
        return redirect()->route('admin.users.show', ['user' => $invite->referral_id])
            ->with(['success' => 'Successfully changed']);
    }
}
