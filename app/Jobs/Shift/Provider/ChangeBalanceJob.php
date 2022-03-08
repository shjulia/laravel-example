<?php

declare(strict_types=1);

namespace App\Jobs\Shift\Provider;

use App\Entities\User\User;
use App\Services\Wallet\Provider\WalletService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ChangeBalanceJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * @var User
     */
    private $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function handle(WalletService $walletService): void
    {
        $balance = $walletService->balance($this->user->wallet->wallet_client_id);
        $this->user->wallet->updateAmount($balance);
        $this->user->wallet->save();
    }
}
