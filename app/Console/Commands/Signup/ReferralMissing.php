<?php

declare(strict_types=1);

namespace App\Console\Commands\Signup;

use App\Entities\Invite\Invite;
use App\Mail\Invite\ReferralMissingMail;
use App\Repositories\Invite\InviteRepository;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

/**
 * Class ReferralMissing
 * Notifies referral to help signup referred users
 *
 * @package App\Console\Commands\Signup
 */
class ReferralMissing extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'referral:missing';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Notify referral to help signup referred users';

    /**
     * @var InviteRepository
     */
    private $inviteRepository;


    /**
     * ReferralMissing constructor.
     * @param InviteRepository $inviteRepository
     */
    public function __construct(InviteRepository $inviteRepository)
    {
        parent::__construct();
        $this->inviteRepository = $inviteRepository;
    }

    public function handle()
    {
        $invites = $this->inviteRepository->findNoRespondInvites();
        $invitedReferrals = [];
        foreach ($invites as $invite) {
            if (in_array($invite->referral_id, $invitedReferrals)) {
                continue;
            }
            array_push($invitedReferrals, $invite->referral_id);
            $this->manageInvites($invite);
        }
    }

    /**
     * @param Invite $invite
     */
    private function manageInvites(Invite $invite): void
    {
        $allInvites = $this->inviteRepository->findNoRespondInvitesForReferral($invite->referral_id);
        Mail::to($invite->referral->user)->send(new ReferralMissingMail($allInvites));
        $this->inviteRepository->updateNotificationMarks($allInvites->pluck('id')->toArray());
    }
}
