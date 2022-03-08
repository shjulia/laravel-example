<?php

declare(strict_types=1);

namespace App\Console\Commands\Wallet;

use App\Entities\Payment\Charge;
use App\Entities\User\Practice\Practice;
use App\Entities\User\User;
use App\Repositories\User\PracticeRepository;
use App\Services\Wallet\Practice\WalletService;
use Illuminate\Console\Command;
use Ramsey\Uuid\Uuid;

class PaymentsToCoreCommand extends Command
{
    protected $signature = 'payments:to-core';
    /**
     * @var WalletService
     */
    private $walletService;
    /**
     * @var PracticeRepository
     */
    private $practices;

    public function __construct(PracticeRepository $practices, WalletService $walletService)
    {
        parent::__construct();
        $this->walletService = $walletService;
        $this->practices = $practices;
    }

    public function handle(): void
    {
        $page = 1;
        while ($practices = $this->practices->findPaginate($page)) {
            if ($practices->isEmpty()) {
                return;
            }
            /** @var Practice $practice */
            foreach ($practices as $practice) {
                /** @var User $user */
                $user = $practice->practiceCreator();
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
                    if (!$practice->stripe_client_id) {
                        continue;
                    }
                    $clientId = $clientId ?? $user->wallet->wallet_client_id;
                    $this->walletService->recordPaymentData(
                        $clientId,
                        $practice->getEncryptedStripeCustomerId()
                    );
                    $user->markWalletAsPaymentDataSet();
                    $user->wallet->save();

                    /** @var Charge $charge */
                    /*foreach ($practice->charges as $charge) {
                        if (strpos($charge->charge_stripe_id, 'ch_') === false) {
                            continue;
                        }
                        $id = Uuid::uuid4()->toString();
                        $status = $charge->charge_status;
                        if (!$status) {
                            $status = $charge->isRefund() ? 'charge.refunded' : 'charge.captured';
                        }
                        $status = in_array($status, ['refunded', 'expired', 'captured', 'uncaptured', 'pending'])
                            ? ('charge.' . $status) : $status;
                        $this->walletService->recordOldPayment([
                            'id' => $clientId,
                            'chargeId' => $id,
                            'amount' => $charge->amount,
                            'purpose' => 'For shift #' . $charge->shift_id,
                            'date' => $charge->created,
                            'payment_system_id' => $charge->charge_stripe_id,
                            'status' => $status,
                            'isMain' => (bool)$charge->is_main,
                            'isCapture' => (bool)$charge->is_capture,
                            'refundAmount' => $charge->refund_amount
                        ]);
                        $charge->update([
                            'charge_stripe_id' => $id
                        ]);
                    }
                    foreach ($practice
                                 ->practiceCreator()
                                 ->invite()
                                 ->where('bonus_value', '!=', null)
                                 ->get()
                        as $charge
                    ) {
                        $id = Uuid::uuid4()->toString();
                        $this->walletService->recordOldTransfer([
                            'id' => $clientId,
                            'chargeId' => $id,
                            'amount' => $charge->bonus_value,
                            'purpose' => 'Referral Bonus for ' . $charge->user_id . ' User',
                            'date' => $charge->created_at->format('Y-m-d H:i:s'),
                            'payment_system' => $charge->payment_system,
                            'payment_system_id' => $charge->charge_id,
                            'expedited' => true,
                            'status' => $charge->payment_status
                        ]);
                    }*/
                } catch (\DomainException $e) {
                    echo $e->getMessage() . ' (' . $practice->id . ')' . PHP_EOL;
                }
                echo $practice->id . PHP_EOL;
            }
            $page++;
        }
    }
}
