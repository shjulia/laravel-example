<?php

namespace App\Repositories\Invite;

use App\Entities\Invite\Invite;
use App\Entities\User\Practice\Practice;
use App\Entities\User\Provider\Specialist;
use App\Entities\User\Referral;

/**
 * Class InviteRepository
 * @package App\Repositories\Invite
 */
class InviteRepository
{
    /**
     * @param int $referralId
     * @return Invite[]
     */
    public function findByReferral(int $referralId)
    {
        return Invite::where('referral_id', $referralId)
            ->orderBy('accepted')
            ->orderBy('updated_at')
            ->get();
    }

    /**
     * @param int $providerId
     * @return Invite|null
     */
    public function findInvitedNotPaidUser(int $userId): ?Invite
    {
        return Invite::where('user_id', $userId)
            ->where('status', null)
            ->first();
    }

    /**
     * @param int $referralId
     * @return Invite[]
     */
    public function findInvitesByReferral(int $referralId)
    {
        return Invite::where('referral_id', $referralId)
            ->with('user.specialist')
            ->orderBy('updated_at', 'DESC')
            ->paginate();
    }

    /**
     * @param int $referralId
     * @return int
     */
    public function findCountByReferral(int $referralId)
    {
        return Invite::where('referral_id', $referralId)->count();
    }

    /**
     * @param Referral $referral
     * @param Invite $invite
     * @return Invite
     */
    public function getByReferrerAndInvite(Referral $referral, Invite $invite): Invite
    {
        $invite = Invite::where('referral_id', $referral->user_id)
            ->where('id', $invite->id)
            ->first();
        if (!$invite) {
            throw new \DomainException('Invite not found');
        }
        return $invite;
    }

    /**
     * @param int $id
     * @return Invite
     */
    public function getInviteById(int $id): Invite
    {
        if (!$invite = Invite::where('id', $id)->first()) {
            throw new \DomainException('Invite not found');
        }
        return $invite;
    }

    /**
     * @param string $chargeId
     * @return Invite|null
     */
    public function findByChargeId(string $chargeId): ?Invite
    {
        return Invite::where('charge_id', $chargeId)->first();
    }

    /**
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function findReferredInvitesPaginate()
    {
        return Invite::whereNotNull('status')
            ->orderBy('updated_at', 'DESC')
            ->with(['user', 'referral.user'])
            ->paginate();
    }

    /**
     * @return Invite[]|\Illuminate\Database\Eloquent\Collection
     */
    public function findNoRespondInvites()
    {
        return Invite::whereHas('user', function ($query) {
                $query->whereNotNull('signup_step')
                    ->orWhere(function ($query) {
                        $query->whereDoesntHave('practices', function ($query) {
                            $query->where('approval_status', Practice::STATUS_APPROVED);
                        })
                            ->whereDoesntHave('specialist', function ($query) {
                                $query->where('approval_status', Specialist::STATUS_APPROVED);
                            });
                    });
        })
            ->where('referral_notified', 0)
            ->where('created_at', '<=', now()->subDays(7)->format('Y-m-d H:i:s'))
            ->get();
    }

    /**
     * @param int $referralId
     * @return Invite[]|\Illuminate\Database\Eloquent\Collection
     */
    public function findNoRespondInvitesForReferral(int $referralId)
    {
        return Invite::where('referral_id', $referralId)
            ->whereHas('user', function ($query) {
                $query->whereNotNull('signup_step')
                ->orWhere(function ($query) {
                    $query->whereDoesntHave('practices', function ($query) {
                        $query->where('approval_status', Practice::STATUS_APPROVED);
                    })
                        ->whereDoesntHave('specialist', function ($query) {
                            $query->where('approval_status', Specialist::STATUS_APPROVED);
                        });
                });
            })
            ->where('created_at', '<=', now()->subDays(7)->format('Y-m-d H:i:s'))
            ->orderBy('referral_notified')
            ->with(['user', 'referral.user'])
            ->get();
    }

    /**
     * @param array $invites
     * @return bool
     */
    public function updateNotificationMarks(array $invites): bool
    {
        return Invite::whereIn('id', $invites)->update([
            'referral_notified' => 1
        ]);
    }
}
