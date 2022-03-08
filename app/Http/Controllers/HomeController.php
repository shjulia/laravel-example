<?php

namespace App\Http\Controllers;

use App\Entities\User\User;
use App\Repositories\Invite\InviteRepository;
use App\Repositories\User\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

/**
 * Class HomeController
 * @package App\Http\Controllers
 */
class HomeController extends Controller
{
    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * HomeController constructor.
     * @param UserRepository $userRepository
     */
    public function __construct(UserRepository $userRepository)
    {
        $this->middleware('auth');
        $this->userRepository = $userRepository;
    }

    /**
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function index()
    {
        /** @var User $user */
        $user = Auth::user();
        if ($user->isProvider()) {
            return $this->provider($user);
        }
        if ($user->isPractice()) {
            return $this->practice($user);
        }
        if ($user->isAdminStatuses()) {
            return redirect()->route('admin.analytics.index');
        }
        if ($user->isCustomerSuccess()) {
            return redirect()->route('admin.users.approvalList');
        }
        if ($user->isAccountant()) {
            return redirect()->route('admin.analytics.transactions.practices');
        }
        return $this->partner($user);
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function notAllow()
    {
        /** @var User $user */
        $user = Auth::user();
        $isOpened = true;
        $city = null;
        /*$isOpened = false;
        if ($user->isProvider()) {
            $city = $user->specialist->driver_city;
            $isOpened = $user->specialist->area_id && $user->specialist->area->isOpen();
        } elseif ($user->isPractice()) {
            $city = $user->practice->city;
            $isOpened = $user->practice->area_id && $user->practice->area->isOpen();
        }*/
        if (env('APP_ALLOW') && $isOpened) {
            return redirect()->route('home');
        }
        return view('home.not-allow', compact('user', 'city'));
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function dashboard()
    {
        return $this->notAllow();
    }

    /**
     * @param User $user
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function provider(User $user)
    {
        if ($user->specialist->isApproved() && $user->specialist->isSetTransferInfo()) {
            return redirect()->route('shifts.provider.index');
        }
        $inviteRepository = app()->get(InviteRepository::class);
        $user = $this->userRepository->getUserProviderFullData($user->id);
        $invitesCount = 0;
        $referral = $user->referral;
        if ($referral) {
            $invitesCount = $inviteRepository->findCountByReferral($referral->user_id);
        }
        return view('home.home-provider', compact('user', 'invitesCount', 'referral'));
    }

    /**
     * @param User $user
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function practice(User $user)
    {
        $user = $this->userRepository->getUserPracticeFullData($user->id);
        if ($user->practice->isApproved() && $user->practice->isSetPaymentInfo()) {
            return redirect()->route('shifts.index')
                ->with(['success' => 'Congratulations! You have been approved and can start hiring providers']);
        }

        return view('home.home-practice', compact('user'));
    }

    /**
     * @param User $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function partner(User $user)
    {
        return redirect()->route('referral.index');
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function setTimeDifference(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();
        if (!session()->get('isAdmin', false)) {
            $this->userRepository->setTimeDifference($request->time, $user->id);
        }
        return response()->json([], Response::HTTP_OK);
    }

    /**
     * Store the PushSubscription.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function subscribeToPush(Request $request)
    {
        $this->validate($request, [
            'endpoint'    => 'required',
            'keys.auth'   => 'required',
            'keys.p256dh' => 'required'
        ]);
        $endpoint = $request->endpoint;
        $token = $request->keys['auth'];
        $key = $request->keys['p256dh'];
        $user = Auth::user();
        $user->updatePushSubscription($endpoint, $key, $token);

        return response()->json(['success' => true], 200);
    }
}
