<?php

declare(strict_types=1);

namespace App\Repositories\Payment;

use App\Entities\Payment\ProviderBonus;
use App\Entities\User\Provider\Specialist;
use Illuminate\Database\Eloquent\Collection;

/**
 * Class ProviderBonusesRepository
 * @package App\Repositories\Payment
 */
class ProviderBonusesRepository
{
    /**
     * @return ProviderBonus[]|Collection
     */
    public function findNotSentToPaidCharges()
    {
        return ProviderBonus::where('status', ProviderBonus::STATUS_IN_BOON)
            ->with('provider')
            ->get();
    }

    /**
     * @return ProviderBonus[]|Collection
     */
    public function findNotSentToPaidChargesProvider(Specialist $provider)
    {
        return ProviderBonus::where('status', ProviderBonus::STATUS_IN_BOON)
            ->with('provider')
            ->get();
    }

    /**
     * @param Specialist $provider
     * @return float
     */
    public function sumNotSentToPaid(Specialist $provider): float
    {
        return $this->findNotSentToPaidChargesProvider($provider)->sum('bonus_value');
    }

    /**
     * @param string $chargeId
     * @return ProviderBonus|null
     */
    public function findByChargeId(string $chargeId): ?ProviderBonus
    {
        return ProviderBonus::where('charge_id', $chargeId)->first();
    }

    /**
     * @return ProviderBonus[]|Collection
     */
    public function findPaginate()
    {
        return ProviderBonus::orderBy('updated_at', 'DESC')->paginate();
    }
}
