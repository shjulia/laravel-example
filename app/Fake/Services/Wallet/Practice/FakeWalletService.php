<?php

declare(strict_types=1);

namespace App\Fake\Services\Wallet\Practice;

use App\Services\Wallet\Practice\WalletService;
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

    public function attachPaymentMethod(string $id, string $token): void
    {
    }

    public function createPayment(string $accountId, float $amount, bool $capture): string
    {
        return Str::uuid()->toString();
    }

    public function refund(string $paymentId, ?float $amount = null): void
    {
    }

    public function capture(string $paymentId): void
    {
    }

    public function recordPaymentData(string $id, string $customerId): void
    {
    }

    public function recordOldPayment(array $data): void
    {
    }

    public function recordOldTransfer(array $data): void
    {
    }
}
