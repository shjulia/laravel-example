<?php

declare(strict_types=1);

namespace App\Services\Wallet\Practice;

use App\Services\Integration\CoreService;

/**
 * Class WalletService
 */
class WalletService
{
    /**
     * @var CoreService
     */
    private $core;

    public function __construct(CoreService $core)
    {
        $this->core = $core;
    }

    public function createClient(
        string $firstName,
        string $lastName,
        ?string $middleName,
        string $email,
        string $date
    ): string {
        return $this->core->request('POST', '/wallet/client/create-base', [
            'firstName' => $firstName,
            'lastName' => $lastName,
            'middleName' => $middleName,
            'email' => $email,
            'date' => $date
        ])['account_id'];
    }

    public function attachPaymentMethod(string $id, string $token): void
    {
        $this->core->request('POST', '/wallet/client/attach-payment-method-by-token', [
            'id' => $id,
            'token' => $token,
        ]);
    }

    public function createPayment(string $accountId, float $amount, bool $capture): string
    {
        return $this->core->request('POST', '/wallet/payment/create', [
            'accountId' => $accountId,
            'amount' => $amount,
            'capture' => $capture
        ])['paymentId'];
    }

    public function refund(string $paymentId, ?float $amount = null): void
    {
        $this->core->request('POST', '/wallet/payment/refund', [
            'paymentId' => $paymentId,
            'amount' => $amount
        ]);
    }

    public function capture(string $paymentId): void
    {
        $this->core->request('POST', '/wallet/payment/capture', [
            'paymentId' => $paymentId
        ]);
    }

    public function recordPaymentData(
        string $id,
        string $customerId
    ): void {
        $this->core->request('POST', '/wallet/client/record-payment-data', [
            'id' => $id,
            'customerId' => $customerId
        ]);
    }

    public function recordOldPayment(array $data): void
    {
        $this->core->request('POST', '/wallet/client/record-old-payment', $data);
    }

    public function recordOldTransfer(array $data): void
    {
        $this->core->request('POST', '/wallet/client/record-old-transfer', $data);
    }
}
