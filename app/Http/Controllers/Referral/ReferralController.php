<?php

namespace App\Http\Controllers\Referral;

use App\Entities\Invite\Invite;
use App\Entities\User\Referral;
use App\Entities\User\User;
use App\Http\Controllers\Controller;
use App\Http\Requests\General\EmailRequest;
use App\Http\Requests\Referral\CodeRequest;
use App\Http\Requests\Referral\InviteRequest;
use App\Repositories\Invite\InviteRepository;
use App\UseCases\Invite\InviteService;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

/**
 * Class ReferralController
 * @package App\Http\Controllers\Referral
 */
class ReferralController extends Controller
{
    /**
     * @var InviteService
     */
    private $inviteService;

    /**
     * @var InviteRepository
     */
    private $inviteRepository;

    /**
     * @var Referral
     */
    private $referral;

    /**
     * ReferralController constructor.
     * @param InviteService $inviteService
     * @param InviteRepository $inviteRepository
     */
    public function __construct(InviteService $inviteService, InviteRepository $inviteRepository)
    {
        $this->inviteService = $inviteService;
        $this->inviteRepository = $inviteRepository;
        $this->middleware(function ($request, $next) {
            /** @var User $user */
            $user = Auth::user();
            if (!$referral = $user->referral) {
                abort(403);
            }
            $this->referral = $referral;
            return $next($request);
        });
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $invitesCount = $this->inviteRepository->findCountByReferral($this->referral->user_id);
        $referral = $this->referral;
        return view('referral.index', compact('invitesCount', 'referral'));
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function invites()
    {
        $invites = $this->inviteRepository->findInvitesByReferral($this->referral->user_id);
        $referral = $this->referral;
        return view('referral.invites', compact('invites', 'referral'));
    }

    /**
     * @param InviteRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function invite(InviteRequest $request)
    {
        try {
            $this->inviteService->invite($request->email, $this->referral);
        } catch (\DomainException $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_BAD_GATEWAY);
        }
        return response()->json([], Response::HTTP_OK);
    }

    /**
     * @param Invite $invite
     * @return \Illuminate\Http\RedirectResponse
     */
    public function reinvite(Invite $invite)
    {
        try {
            $invite = $this->inviteRepository->getByReferrerAndInvite($this->referral, $invite);
            $this->inviteService->resendInvite($this->referral, $invite);
        } catch (\DomainException $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_BAD_GATEWAY);
        }
        return response()->json([], Response::HTTP_OK);
    }

    /**
     * @param CodeRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function changeCode(CodeRequest $request)
    {
        try {
            $this->inviteService->changeCode($this->referral, $request->code);
        } catch (\DomainException $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_BAD_GATEWAY);
        }
        return response()->json([], Response::HTTP_OK);
    }
}
