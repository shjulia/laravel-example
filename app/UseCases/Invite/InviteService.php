<?php

declare(strict_types=1);

namespace App\UseCases\Invite;

use App\Entities\Invite\Invite;
use App\Entities\User\Referral;
use App\Events\Invite\InviteEvent;
use App\Repositories\Invite\InviteRepository;
use Carbon\Carbon;
use Illuminate\Contracts\Events\Dispatcher;

/**
 * Class InviteService
 * Works on new users invites and referral code change.
 *
 * @package App\UseCases\Invite
 */
class InviteService
{
    /**
     * @var InviteRepository
     */
    private $inviteRepository;

    /**
     * @var Dispatcher
     */
    private $dispatcher;

    /**
     * InviteService constructor.
     * @param InviteRepository $inviteRepository
     * @param Dispatcher $dispatcher
     */
    public function __construct(InviteRepository $inviteRepository, Dispatcher $dispatcher)
    {
        $this->inviteRepository = $inviteRepository;
        $this->dispatcher = $dispatcher;
    }

    /**
     * @param string $email
     * @param Referral $referral
     */
    public function invite(string $email, Referral $referral): void
    {
        /** @var Invite $invite */
        $invite = Invite::make([
           'referral_id' => $referral->user_id,
           'email' => $email
        ]);
        $invite->setNotAcceptedStatus();
        if (!$invite->save()) {
            throw new \DomainException('Invite creating error');
        }
        $this->dispatcher->dispatch(new InviteEvent($invite));
    }

    /**
     * @param Referral $referral
     * @param Invite $invite
     */
    public function resendInvite(Referral $referral, Invite $invite): void
    {
        if ($referral->user_id != $invite->referral_id) {
            throw new \DomainException('Invite changing not allowed');
        }
        $invite->update([
            'updated_at' => Carbon::now()
        ]);
        $this->dispatcher->dispatch(new InviteEvent($invite));
    }

    /**
     * @param Referral $referral
     * @param string $code
     */
    public function changeCode(Referral $referral, string $code): void
    {
        $referral->update([
            'referral_code' => $code
        ]);
    }
}
