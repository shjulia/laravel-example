<?php

use App\Entities\Payment\ProviderCharge;
use App\Entities\Shift\Shift;
use Illuminate\Database\Seeder;

/**
 * Class CreateChargesLogFromShifts
 */
class CreateChargesLogFromShifts extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $shifts = Shift::where('status', Shift::STATUS_FINISHED)
            ->orWhere(function ($query) {
                $query->where('status', Shift::STATUS_ACCEPTED_BY_PROVIDER)
                    ->where('processed', 1);
            })
            ->whereDoesntHave('providerCharges')
            ->get();
        foreach ($shifts as $shift) {
            $charge = ProviderCharge::createCharge(
                $shift,
                $shift->cost,
                'dwolla',
                true
            );
            $charge->setStatus(ProviderCharge::STATUS_PAID);
            $charge->created_at = \Carbon\Carbon::createFromTimeString($shift->end_date . ' ' . $shift->to_time);
            $charge->updated_at = \Carbon\Carbon::createFromTimeString($shift->end_date . ' ' . $shift->to_time);
            $charge->save();
        }
    }
}
