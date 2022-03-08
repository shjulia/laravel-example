<?php

declare(strict_types=1);

namespace App\UseCases\Shift;

use App\Entities\User\User;
use App\Repositories\Payment\ProviderChargeRepository;
use App\Services\Wallet\Provider\WalletService;

/**
 * Class PaymentService
 * Provider payments, referral payments and bonuses.
 * @package App\UseCases\Shift
 */
class PaymentService
{
    /**
     * @var ProviderChargeRepository
     */
    private $providerChargeRepository;
    /**
     * @var WalletService
     */
    private $walletService;

    public function __construct(
        ProviderChargeRepository $providerChargeRepository,
        WalletService $walletService
    ) {
        $this->providerChargeRepository = $providerChargeRepository;
        $this->walletService = $walletService;
    }

    public function replenish(User $user, float $amount, string $purpose): void
    {
        $this->walletService->replenish($user->wallet->wallet_client_id, $amount, $purpose);
    }

    public function withdraw(
        User $user,
        ?float $amount,
        ?bool $isExpedited = false,
        ?bool $withCommission = false
    ): void {
        $this->walletService->withdraw($user->wallet->wallet_client_id, $amount, $isExpedited, $withCommission);
    }

    public function replenishAndWithDraw(
        User $user,
        float $amount,
        string $purpose,
        ?bool $isExpedited = false,
        ?bool $withCommission = false
    ) {
        $this->replenish($user, $amount, $purpose);
        $this->withdraw($user, $amount, $isExpedited, $withCommission);
    }
}
