<?php

declare(strict_types=1);

namespace App\Jobs\Shift\Provider;

use App\Entities\Notification\EmailMark;
use App\Entities\Payment\ProviderBonus;
use App\Entities\Shift\Shift;
use App\Mail\Shift\Bonus\RoadWarriorMail;
use App\Repositories\Data\DistanceRepository;
use App\Repositories\Notification\EmailMarkRepository;
use App\Services\Maps\DistanceService;
use App\UseCases\Shift\PaymentService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

/**
 * Class CheckDistanceJob
 * @package App\Jobs\Shift\Provider
 */
class CheckDistanceJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * @var Shift
     */
    private $shift;

    /**
     * CheckDistanceJob constructor.
     * @param Shift $shift
     */
    public function __construct(Shift $shift)
    {
        $this->shift = $shift;
    }

    public function handle(
        DistanceService $distanceService,
        DistanceRepository $distanceRepository,
        PaymentService $paymentService,
        EmailMarkRepository $emailMarkRepository
    ) {
        $distance = $this->getDistanceMiles($distanceService, $distanceRepository);
        if ($distance < 50) {
            return;
        }
        $provider = $this->shift->provider;
        $sent = $emailMarkRepository->wasEmailSent($provider->user, EmailMark::ROAD_WARRIOR);
        if ($sent) {
            return;
        }
        $bonus = ProviderBonus::ROAD_WARRIOR_BONUS_VAL;
        try {
            $paymentService->replenishAndWithDraw(
                $provider->user,
                $bonus,
                'Road warrior bonus',
                true,
                false
            );
            Mail::to($provider->user->email)->send(new RoadWarriorMail($provider, $bonus, $distance));
            $emailMark = EmailMark::createMark($provider->user, EmailMark::ROAD_WARRIOR);
            $emailMark->saveOrFail();
        } catch (\Throwable $e) {
            \LogHelper::error($e);
        }
    }

    /**
     * @param DistanceService $distanceService
     * @param DistanceRepository $distanceRepository
     * @return float
     */
    private function getDistanceMiles(DistanceService $distanceService, DistanceRepository $distanceRepository): float
    {
        $location = $this->shift->practice_location;
        if (
            $distance = $distanceRepository->findFullDistance(
                $this->shift->provider_id,
                $this->shift->practice_id,
                $location->addressId
            )
        ) {
            return $distance->getDistanceInMiles();
        }
        $distance = $distanceService->getDistance($location->fullAddress(), $this->shift->provider->full_address);
        if ($distance) {
            $distance = $distanceRepository->createDistance(
                $this->shift->provider_id,
                $this->shift->practice_id,
                $location->addressId,
                $distance
            );
            return $distance ? $distance->getDistanceInMiles() : 0;
        }
        return 0;
    }
}
