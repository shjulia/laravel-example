<?php

declare(strict_types=1);

namespace App\Services\Wallet\Provider;

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

    public function attachTransferData(string $id, string $routingNumber, string $accountNumber): void
    {
        $this->core->request('POST', '/wallet/client/set-transfer-data', [
            'id' => $id,
            'routingNumber' => $routingNumber,
            'accountNumber' => $accountNumber
        ]);
    }

    public function recordTransferData(
        string $id,
        string $customerId,
        string $routingNumber,
        string $accountNumber,
        string $fundingSourcesId
    ): void {
        $this->core->request('POST', '/wallet/client/record-transfer-data', [
            'id' => $id,
            'customerId' => $customerId,
            'routingNumber' => $routingNumber,
            'accountNumber' => $accountNumber,
            'fundingSourcesId' => $fundingSourcesId
        ]);
    }

    public function replenish(string $id, float $amount, string $purpose): void
    {
        $this->core->request('POST', '/wallet/client/replenish', [
            'id' => $id,
            'amount' => $amount,
            'purpose' => $purpose
        ]);
    }

    public function withdraw(string $id, ?float $amount, bool $expedited, bool $withCommission): string
    {
        return $this->core->request('POST', '/wallet/payment/withdraw', [
            'accountId' => $id,
            'amount' => $amount,
            'expedited' => $expedited,
            'withCommission' => $withCommission
        ])['transactionId'];
    }

    public function balance(string $id): float
    {
        return $this->core->fetch('GET', '/wallet/client/' . $id . '/view')['client']['wallet']['amount'];
    }

    public function recordOldTransfer(array $data): void
    {
        $this->core->request('POST', '/wallet/client/record-old-transfer', $data);
    }
}
