<?php

declare(strict_types=1);

namespace App\Repositories\Payment;

use App\Entities\Payment\ProviderCharge;
use App\Entities\Shift\Shift;
use App\Entities\User\Provider\Specialist;
use Illuminate\Database\Eloquent\Collection;

/**
 * Class ProviderChargeRepository
 * @package App\Repositories\Payment
 */
class ProviderChargeRepository
{
    /**
     * @param Shift $shift
     * @return ProviderCharge[]|Collection
     */
    public function findChargesToRecalculate(Shift $shift)
    {
        return ProviderCharge::where('shift_id', $shift->id)
            ->where('status', ProviderCharge::STATUS_IN_BOON)
            ->get();
    }

    /**
     * @return ProviderCharge[]|Collection
     */
    public function findNotSentToPaidCharges()
    {
        return ProviderCharge::where('status', ProviderCharge::STATUS_IN_BOON)
            ->get();
    }

    /**
     * @param Shift $shift
     * @return ProviderCharge|null
     */
    public function findMainCharge(Shift $shift): ?ProviderCharge
    {
        return ProviderCharge::where('shift_id', $shift->id)
            ->where('status', ProviderCharge::STATUS_IN_BOON)
            ->where('is_main', 1)
            ->first();
    }

    /**
     * @param Specialist $provider
     * @return ProviderCharge[]|Collection
     */
    public function findNotSentChargesForProvider(Specialist $provider)
    {
        return ProviderCharge::where('provider_id', $provider->user_id)
            ->where('status', ProviderCharge::STATUS_IN_BOON)
            ->get();
    }

    /**
     * @param Specialist $provider
     * @return float
     */
    public function sumNotSentToPaid(Specialist $provider): float
    {
        $debt = $provider->debt;
        return $this->findNotSentChargesForProvider($provider)->sum('amount') - $debt;
    }

    /**
     * @return ProviderCharge[]
     */
    public function findAllPaginate()
    {
        return ProviderCharge::orderBy('updated_at', 'DESC')->with(['shift', 'provider.user'])->paginate();
    }

    /**
     * @param string $chargeId
     * @return ProviderCharge
     */
    public function findByChargeId(string $chargeId): ?ProviderCharge
    {
        return ProviderCharge::where('charge_id', $chargeId)->first();
    }

    /**
     * @param string $chargeId
     * @return ProviderCharge
     */
    public function getByChargeId(string $chargeId): ProviderCharge
    {
        $charge = $this->findByChargeId($chargeId);
        if (!$charge) {
            throw new \DomainException('Provider charge not found');
        }
        return $charge;
    }
}
