<?php

declare(strict_types=1);

namespace App\Entities\User\Wallet;

use App\Entities\User\User;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $user_id
 * @property string|null $wallet_client_id
 * @property bool $has_payment_data
 * @property bool $has_transfer_data
 * @property float $balance
 * @property-read User $user
 * Class Wallet
 */
class Wallet extends Model
{
    /**
     * Has relation one to one with User
     *
     * @var string
     */
    protected $primaryKey = "user_id";
    /**
     * @var array
     */
    protected $guarded = [];
    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    /**
     * @param User $user
     * @param string $walletClientId
     * @return static
     */
    public static function createBase(User $user, string $walletClientId): self
    {
        $wallet = new self();
        $wallet->user_id = $user->id;
        $wallet->wallet_client_id = $walletClientId;
        $wallet->has_payment_data = false;
        $wallet->has_transfer_data = false;
        $wallet->balance = 0.0;
        return $wallet;
    }

    public function markAsPaymentDataSet(): void
    {
        $this->has_payment_data = true;
    }

    public function markAsTransferDataSet(): void
    {
        $this->has_transfer_data = true;
    }

    public function updateAmount(float $amount): void
    {
        $this->balance = $amount;
    }
}
