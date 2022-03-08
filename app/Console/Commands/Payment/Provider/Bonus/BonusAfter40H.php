<?php

declare(strict_types=1);

namespace App\Console\Commands\Payment\Provider\Bonus;

use App\Entities\Payment\ProviderBonus;
use App\Entities\User\Provider\Specialist;
use App\Mail\Shift\Bonus\BonusAfter40HMail;
use App\UseCases\Shift\PaymentService;
use Illuminate\Console\Command;
use Illuminate\Contracts\Mail\Mailer;

/**
 * Class BonusAfter40H
 * Gives provider a bonus after he have worked more than 40 hours.
 * Sends email to notify about the bonus.
 *
 * @package App\Console\Commands\Payment\Provider\Bonus
 */
class BonusAfter40H extends Command
{
    /**
     * @var string
     */
    protected $signature = 'bonus:after40h';

    /**
     * @var string
     */
    protected $description = 'Pay $25 to providers after 40h work';

    public function handle(PaymentService $paymentService, Mailer $mailer)
    {
        $providers = Specialist::where('hours_total', '>=', ProviderBonus::FIRST_H)
            ->whereDoesntHave('providerBonuses', function ($query) {
                $query->where('bonus_h', ProviderBonus::FIRST_H);
            })
            ->get();

        foreach ($providers as $provider) {
            $providerBonus = ProviderBonus::createCharge(
                $provider,
                $amount = 25.0,
                ProviderBonus::FIRST_H
            );
            $providerBonus->save();
            try {
                $paymentService->replenishAndWithDraw(
                    $provider->user,
                    $amount,
                    'Bonus after 40h worked',
                    true,
                    true
                );
                $mailer->to($provider->user->email)->send(new BonusAfter40HMail());
            } catch (\DomainException $e) {
                continue;
            }
        }
    }
}
