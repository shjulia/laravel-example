<?php

declare(strict_types=1);

namespace App\Jobs\Emails;

use App\Entities\User\User;
use App\Mail\Mailing\ReferralInfoMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

/**
 * Class ReferralInfoJob
 * @package App\Jobs\Emails
 */
class ReferralInfoJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * @var bool
     */
    public $toAll;

    /**
     * ReferralInfoJob constructor.
     * @param bool $toAll
     */
    public function __construct(bool $toAll = true)
    {
        $this->toAll = $toAll;
    }

    public function handle(): void
    {
        $offset = 0;
        $users = $this->findUsers(0);
        while (!$users->isEmpty()) {
            foreach ($users as $user) {
                Mail::to($user->email)->send(new ReferralInfoMail($user));
                sleep(1);
            }
            $offset += 50;
            $users = $this->findUsers($offset);
        }
    }

    /**
     * @param int $offset
     * @return User[]|\Illuminate\Database\Eloquent\Collection
     */
    private function findUsers(int $offset)
    {
        $users = User::whereHas('referral')
            ->with('referral');
        if (!$this->toAll) {
            $users->whereIn('id', [16,280,176]);
        }
        $users = $users->limit(50)
            ->offset($offset)
            ->get();
        return $users;
    }
}
