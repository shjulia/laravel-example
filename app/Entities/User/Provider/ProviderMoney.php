<?php

declare(strict_types=1);

namespace App\Entities\User\Provider;

use Illuminate\Database\Eloquent\Model;

/**
 * @deprecated
 * Class ProviderMoney
 *
 * @package App\Entities\User\Provider
 * @mixin \Eloquent
 * @property int $provider_id
 * @property float $earns
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class ProviderMoney extends Model
{
    /** @inheritdoc */
    protected $primaryKey = 'provider_id';

    /** @inheritdoc */
    protected $table = 'provider_money';

    /** @inheritdoc */
    protected $guarded = [];
}
