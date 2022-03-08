<?php

declare(strict_types=1);

namespace App\Entities\User;

use App\Helpers\EncryptHelper;
use Illuminate\Database\Eloquent\Model;

/**
 * @deprecated
 * FundingSource - User wallet
 *
 * @property int $id
 * @property int $user_id
 * @property string $routing_number
 * @property string $account_number
 * @property string $funding_source_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @mixin \Eloquent
 */
class FundingSource extends Model
{
    /**
     * @var array
     */
    protected $fillable = [
        'user_id',
        'routing_number',
        'account_number',
        'funding_source_id'
    ];

    /**
     * @param string $value
     * @return string
     */
    public function getRoutingNumberAttribute(string $value)
    {
        return EncryptHelper::decrypt($value);
    }

    /**
     * @param string $value
     * @return string
     */
    public function getAccountNumberAttribute(string $value)
    {
        return EncryptHelper::decrypt($value);
    }

    /**
     * @param string $value
     */
    public function setRoutingNumberAttribute(string $value)
    {
        $this->attributes['routing_number'] = EncryptHelper::encrypt($value);
    }

    /**
     * @param string $value
     */
    public function setAccountNumberAttribute(string $value)
    {
        $this->attributes['account_number'] = EncryptHelper::encrypt($value);
    }
}
