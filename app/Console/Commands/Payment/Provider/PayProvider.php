<?php

declare(strict_types=1);

namespace App\Console\Commands\Payment\Provider;

use App\Entities\User\Provider\Specialist;
use App\Jobs\Shift\Provider\PaymentJob;
use App\Repositories\Payment\ProviderChargeRepository;
use App\Repositories\User\UserRepository;
use Illuminate\Console\Command;

/**
 * Class PayProvider
 * Find Providers who didn't receive their payments and pay.
 *
 * @package App\Console\Commands\Payment\Provider
 */
class PayProvider extends Command
{
    /**
     * @var string
     */
    protected $signature = 'pay:provider';

    /**
     * @var string
     */
    protected $description = 'Pay to providers';

    /**
     * @param UserRepository $usersRepository
     */
    public function handle(UserRepository $usersRepository)
    {
        $users = $usersRepository->findUsersWithFullWallet();
        foreach ($users as $user) {
            try {
                PaymentJob::dispatch($user, null, true, false);
                sleep(4);
            } catch (\DomainException $e) {
                \LogHelper::error($e, ['message' => "Can't withdraw money at charge " . $user->id]);
            }
        }
    }
}
