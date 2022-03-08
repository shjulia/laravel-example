<?php

namespace App\Http\Controllers\Admin\Users;

use App\Entities\Industry\Position;
use App\Entities\User\Export\Autosaves;
use App\Entities\User\Export\Users;
use App\Entities\User\Practice\Practice;
use App\Entities\User\Provider\Specialist;
use App\Entities\User\User;
use App\Http\Requests\Admin\User\Edit\InviterRequest;
use App\Repositories\Data\RateRepository;
use App\Repositories\Data\StatesRepository;
use App\Repositories\User\LoginLogRepository;
use App\Repositories\User\RolesRepository;
use App\Repositories\User\UserRepository;
use App\UseCases\Admin\Manage\Users\UsersService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\Mail\MailgunService;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;

/**
 * Class UsersController
 * @package App\Http\Controllers\Admin\Users
 */
class UsersController extends Controller
{
    /*private $register;*/

    /**
     * @var RolesRepository
     */
    private $rolesRepository;

    /**
     * @var UserRepository
     */
    private $usersRepository;

    /**
     * @var StatesRepository
     */
    private $statesRepository;

    /**
     * @var UsersService
     */
    private $usersService;

    /**
     * @var MailgunService
     */
    private $mailgunService;

    public function __construct(
        UserRepository $userRepository,
        RolesRepository $rolesRepository,
        UsersService $usersService,
        StatesRepository $statesRepository,
        MailgunService $mailgunService
    ) {
        $this->middleware('can:manage-users')->except('index', 'show');

        $this->usersRepository = $userRepository;
        $this->rolesRepository = $rolesRepository;
        $this->usersService = $usersService;
        $this->statesRepository = $statesRepository;
        $this->mailgunService = $mailgunService;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $test = $request->test;
        $withRejected = $request->rejected;
        $deactivated = $request->deactivated;
        $users = $this->usersRepository->findByQueryParams(
            $request,
            $test ?: false,
            (bool)$withRejected,
            (bool)$deactivated
        );
        $statuses = Specialist::getStatusList();
        $roles = $this->rolesRepository->findAll();
        $positions = Position::get();

        return view(
            'admin.users.index',
            compact('users', 'statuses', 'roles', 'test', 'positions', 'withRejected', 'deactivated')
        );
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function approvalList()
    {
        $lists = $this->usersRepository->findListsForApproval();
        return view('admin.users.approval-list', compact('lists'));
    }

    /**
     * @param User $user
     * @param RateRepository $rateRepository
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show(User $user, RateRepository $rateRepository)
    {
        $user = $this->usersRepository->getUserWithFullData($user);
        $rates = $rateRepository->findPaginate();
        return view('admin.users.show.show', compact('user', 'rates'));
    }

    /**
     * @param Practice $practice
     * @return \Illuminate\Http\RedirectResponse
     */
    public function practice(Practice $practice)
    {
        $user = $practice->practiceCreator();
        return redirect()->route('admin.users.show', $user);
    }

    /**
     * @param User $user
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showEmails(User $user)
    {
        $emails = $this->usersService->getEmailLogs($user);
        $tz = Auth::user()->tz ?? 0;
        return view('admin.users.show.emails', compact('user', 'emails', 'tz'));
    }

    /**
     * @param User $user
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showApproves(User $user)
    {
        return view('admin.users.show.approval-log', compact('user'));
    }

    public function showLogins(User $user, LoginLogRepository $logRepository)
    {
        $logs = $logRepository->findByUser($user);
        return view('admin.users.show.login-log', compact('logs', 'user'));
    }

    /**
     * @param User $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function approvePractice(User $user)
    {
        /** @var User $admin */
        $admin = Auth::user();
        try {
            $this->usersService->approvePractice($user, $admin);
        } catch (\DomainException $e) {
            return back()->with(['error' => $e->getMessage()]);
        }
        return back()->with(['success' => 'User status changed']);
    }

    /**
     * @param User $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkApprove(User $user)
    {
        $can = $user->specialist->canBeApproved();
        return response()->json($can, Response::HTTP_OK);
    }

    public function approveProvider(User $user, Request $request)
    {
        /** @var User $admin */
        $admin = Auth::user();
        try {
            $this->usersService->approveProvider($user, $admin, $request->approval_reason);
        } catch (\DomainException $e) {
            return back()->with(['error' => $e->getMessage()]);
        }
        return back()->with(['success' => 'User status changed']);
    }

    /**
     * @param User $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function reject(User $user)
    {
        /** @var User $admin */
        $admin = Auth::user();
        try {
            $this->usersService->reject($user, $admin);
        } catch (\DomainException $e) {
            return back()->with(['error' => $e->getMessage()]);
        }
        return back()->with(['success' => 'User status changed']);
    }

    /**
     * @param User $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function unReject(User $user)
    {
        /** @var User $admin */
        $admin = Auth::user();
        try {
            $this->usersService->unReject($user, $admin);
        } catch (\DomainException $e) {
            return back()->with(['error' => $e->getMessage()]);
        }
        return back()->with(['success' => 'User status changed']);
    }

    /**
     * @param User $user
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function destroy(User $user)
    {
        try {
            $this->usersService->deleteUser($user);
        } catch (\DomainException $e) {
            return back()->with(['error' => $e->getMessage()]);
        }
        return redirect()->route('admin.users.index')->with(['success' => 'User deleted']);
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        return view('admin.users.create');
    }

    /**
     * @param User $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function compare(User $user)
    {
        try {
            $this->usersService->compare($user);
        } catch (\DomainException $e) {
            return back()->with(['error' => $e->getMessage()]);
        }
        return redirect()->route('admin.users.show', $user)->with(['success' => 'User photos compared']);
    }

    /**
     * @param User $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function setUserToTest(User $user)
    {
        try {
            $this->usersService->setUserToTest($user);
        } catch (\DomainException $e) {
            return back()->with(['error' => $e->getMessage()]);
        }
        return redirect()->back()
            ->with(['success' => 'User set to test']);
    }

    /**
     * resend message
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function resendMessage(Request $request)
    {
        try {
            $this->usersService->resendMessage($request->id);
            return response('Message has been sent successfully', 200);
        } catch (\Exception $exception) {
            return response('Something goes wrong', 502);
        }
    }

    /**
     * @param User $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function loginAs(User $user)
    {
        Auth::logout();
        session(['isAdmin' => true]);
        Auth::login($user);
        return redirect()->route('home')->with(
            ['success' => 'You have been successfully logged on as ' . $user->full_name]
        );
    }

    public function deactivatedList()
    {
        $lists = $this->usersRepository->findListsForApproval();
        return view('admin.users.approval-list', compact('lists'));
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function inviter(User $user)
    {
        $worked = $this->usersService->checkReferredIsWorked($user);
        return view('admin.users.edit.inviter', compact('user', 'worked'));
    }

    /**
     * @param User $user
     * @param InviterRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function setInviter(User $user, InviterRequest $request)
    {
        try {
            $this->usersService->setInviter($user, $request);
        } catch (\DomainException $e) {
            return back()->with(['error' => $e->getMessage()]);
        }
        return redirect()->route('admin.users.show', $user)->with(['success' => 'User updated successfully']);
    }

    public function resetPasswordEmail(User $user)
    {
        try {
            Password::broker()->sendResetLink(['email' => $user->email]);
        } catch (\DomainException $e) {
            return back()->with(['error' => $e->getMessage()]);
        }
        return redirect()->route('admin.users.show', $user)->with(
            ['success' => "\"Reset password\" Email was successfully sent"]
        );
    }

    /**
     * @return Response|\Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function exportUsers()
    {
        return (new Users())
            ->download('Boon-Users-Export.xlsx');
    }

    /**
     * @return Response|\Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function exportAutosaves()
    {
        return (new Autosaves())
            ->download('Boon-Signup-AutoSave-Export.xlsx');
    }
}
