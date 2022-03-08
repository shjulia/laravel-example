<?php

declare(strict_types=1);

namespace App\Jobs\Shift\Provider;

use App\Entities\User\User;
use App\UseCases\Admin\Notifications\PaymentsProblemService;
use App\UseCases\Shift\PaymentService;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Class PaymentJob
 * @package App\Jobs\Shift\Provider
 */
class PaymentJob
{
    use Dispatchable;
    use SerializesModels;

    /**
     * @var bool|null
     */
    private $isExpedited;
    /**
     * @var bool|null
     */
    private $withCommission;
    /**
     * @var float|null
     */
    private $amount;
    /**
     * @var User
     */
    private $user;


    public function __construct(
        User $user,
        ?float $amount,
        ?bool $isExpedited = false,
        ?bool $withCommission = true
    ) {
        $this->isExpedited = $isExpedited;
        $this->withCommission = $withCommission;
        $this->amount = $amount;
        $this->user = $user;
    }

    /**
     * @param PaymentService $paymentService
     * @param PaymentsProblemService $paymentsProblemService
     */
    public function handle(PaymentService $paymentService, PaymentsProblemService $paymentsProblemService)
    {
        if (!$this->user->wallet) {
            throw new \DomainException('User is not set bank data.');
        }
        if ($this->user->wallet->balance <= 0) {
            throw new \DomainException('Not enough money.');
        }
        try {
            $paymentService->withdraw($this->user, $this->amount, $this->isExpedited, $this->withCommission);
        } catch (\Throwable $e) {
            $paymentsProblemService->notify($this->user);
            \LogHelper::error($e);
            throw $e;
        }
    }
}
