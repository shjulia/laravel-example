<?php

namespace App\Repositories\Payment;

use App\Entities\Payment\Charge;
use App\Entities\Shift\Shift;

/**
 * Class ChargeRepository
 * @package App\Repositories\Payment
 */
class ChargeRepository
{
    /**
     * @param Shift $shift
     * @return Charge[]|\Illuminate\Database\Eloquent\Collection
     */
    public function getNotRefundedCharges(Shift $shift)
    {
        return Charge::where(['shift_id' => $shift->id, 'is_refund' => 0])
            ->whereNotIn('charge_status', [
                Charge::CHARGE_STATUS_EXPIRED,
                Charge::CHARGE_STATUS_REFUNDED,
                Charge::CHARGE_STATUS_FAILED
            ])
            ->get();
    }

    /**
     * @param Shift $shift
     * @return Charge|\Illuminate\Database\Eloquent\Model|object|null
     */
    public function getLastCharge(Shift $shift)
    {
        return Charge::where([
                'shift_id' => $shift->id,
                'is_refund' => 0,
                'is_main' => 1
            ])
            ->whereNotIn('charge_status', [
                Charge::CHARGE_STATUS_EXPIRED,
                Charge::CHARGE_STATUS_REFUNDED,
                Charge::CHARGE_STATUS_FAILED
            ])
            ->orderBy('id', 'DESC')
            ->first();
    }

    /**
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|Charge[]
     */
    public function findAllPaginate()
    {
        return Charge::orderBy('id', 'DESC')->with(['shift'])->paginate(10);
    }
}
