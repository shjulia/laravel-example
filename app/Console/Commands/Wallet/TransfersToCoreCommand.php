<?php

declare(strict_types=1);

namespace App\Console\Commands\Wallet;

use App\Entities\Payment\ProviderCharge;
use App\Entities\User\User;
use App\Repositories\User\SpecialistRepository;
use App\Services\Wallet\Provider\WalletService;
use Illuminate\Console\Command;
use Ramsey\Uuid\Uuid;

class TransfersToCoreCommand extends Command
{
    protected $signature = 'transfers:to-core';
    /**
     * @var SpecialistRepository
     */
    private $providers;
    /**
     * @var WalletService
     */
    private $walletService;

    public function __construct(SpecialistRepository $providers, WalletService $walletService)
    {
        parent::__construct();
        $this->providers = $providers;
        $this->walletService = $walletService;
    }

    public function handle(): void
    {
        $page = 1;
        while ($providers = $this->providers->findPaginate($page)) {
            if ($providers->isEmpty()) {
                return;
            }
            foreach ($providers as $provider) {
                /** @var User $user */
                $user = $provider->user;
                try {
                    if (!$user->wallet) {
                        echo 'No wallet' . $user->id . PHP_EOL;
                        continue;
                        $clientId = $this->walletService->createClient(
                            $user->first_name,
                            $user->last_name,
                            null,
                            $user->email,
                            $user->created_at->format('Y-m-d H:i:s')
                        );
                        $user->createWallet($clientId);
                        $user->wallet->save();
                    }
                    $clientId = $clientId ?? $user->wallet->wallet_client_id;
                    if ($bankDetails = $user->bankDetails()) {
                        $this->walletService->recordTransferData(
                            $clientId,
                            $user->dwolla_customer_id,
                            $bankDetails->routing_number,
                            $bankDetails->account_number,
                            $bankDetails->funding_source_id
                        );
                        $user->markWalletAsTransferDataSet();
                        $user->wallet->save();
                    }
                    /** @var ProviderCharge $charge */
                    /*foreach ($provider->providerCharges as $charge) {
                        $id = Uuid::uuid4()->toString();
                        $this->walletService->recordOldTransfer([
                            'id' => $clientId,
                            'chargeId' => $id,
                            'amount' => $charge->amount,
                            'purpose' => 'For shift #' . $charge->shift_id,
                            'date' => $charge->created_at->format('Y-m-d H:i:s'),
                            'payment_system' => $charge->payment_system,
                            'payment_system_id' => $charge->charge_id ?: '--',
                            'expedited' => !!$charge->expedited_commission_amount,
                            'commission' => ($charge->expedited_commission_amount ?: 0) + ($charge->commission ?: 0),
                            'status' => $charge->payment_status ?: 'created'
                        ]);
                    }

                    foreach ($provider->providerBonuses as $charge) {
                        $id = Uuid::uuid4()->toString();
                        $this->walletService->recordOldTransfer([
                            'id' => $clientId,
                            'chargeId' => $id,
                            'amount' => $charge->bonus_value,
                            'purpose' => 'Bonus ' . $charge->bonus_h ? ($charge->bonus_h . 'h') : $charge->desc,
                            'date' => $charge->created_at->format('Y-m-d H:i:s'),
                            'payment_system' => $charge->payment_system,
                            'payment_system_id' => $charge->charge_id ?: '--',
                            'expedited' => true,
                            'status' => $charge->payment_status ?: 'created'
                        ]);
                    }
                    foreach ($provider->user->invite()->where('bonus_value', '!=', null)->get() as $charge) {
                        $id = Uuid::uuid4()->toString();
                        $this->walletService->recordOldTransfer([
                            'id' => $clientId,
                            'chargeId' => $id,
                            'amount' => $charge->bonus_value,
                            'purpose' => 'Referral Bonus for ' . $charge->user_id . ' User',
                            'date' => $charge->created_at->format('Y-m-d H:i:s'),
                            'payment_system' => $charge->payment_system,
                            'payment_system_id' => $charge->charge_id ?: '--',
                            'expedited' => true,
                            'status' => $charge->payment_status ?: 'created'
                        ]);
                    }*/
                } catch (\DomainException $e) {
                    echo $e->getMessage() . ' (' . $provider->user_id . ')' . PHP_EOL;
                }
                echo $provider->user_id . PHP_EOL;
            }
            $page++;
        }
    }
}
