<?php

namespace App\Repositories\Shift;

use App\Entities\User\Provider\ProviderMoney;
use App\Entities\User\Provider\Specialist;

/**
 * Class ProviderMoneyRepository
 * @package App\Repositories\Shift
 */
class ProviderMoneyRepository
{
    /**
     * @param Specialist $provider
     * @return ProviderMoney|\Illuminate\Database\Eloquent\Model|object|null
     */
    public function getEarnings(Specialist $provider)
    {
        return ProviderMoney::where('provider_id', $provider->user_id)->first();
    }
}
