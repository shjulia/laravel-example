<?php

declare(strict_types=1);

namespace App\Fake\Services\Wallet\Provider;

use App\Services\Wallet\Provider\WalletService;
use Illuminate\Support\Str;

class FakeWalletService extends WalletService
{
    public function createClient(
        string $firstName,
        string $lastName,
        ?string $middleName,
        string $email,
        string $date
    ): string {
        return Str::uuid()->toString();
    }

    public function attachTransferData(string $id, string $routingNumber, string $accountNumber): void
    {
    }

    public function recordTransferData(
        string $id,
        string $customerId,
        string $routingNumber,
        string $accountNumber,
        string $fundingSourcesId
    ): void {
    }

    public function replenish(string $id, float $amount, string $purpose): void
    {
    }

    public function withdraw(string $id, ?float $amount, bool $expedited, bool $withCommission): string
    {
        return Str::uuid()->toString();
    }

    public function balance(string $id): float
    {
        return random_int(10, 3000);
    }

    public function recordOldTransfer(array $data): void
    {
    }
}
