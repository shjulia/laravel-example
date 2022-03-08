<?php

use App\Entities\Shift\Shift;
use App\Entities\User\Practice\Practice;
use App\Entities\User\Provider\Specialist;
use Illuminate\Database\Seeder;

class RecalcTotalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $shifts = Shift::whereHas('practice', function ($query) {
            $query->where('paid_total', '>', 0);
        })
            ->orWhereHas('provider', function ($query) {
                $query->where('paid_total', '>', 0);
            })
            ->get();
        foreach ($shifts as $shift) {
            if ($shift->practice) {
                $shift->practice->update([
                    'hires_total' => 0,
                    'paid_total' => 0
                ]);
            }
            if ($shift->provider) {
                $shift->provider->update([
                    'paid_total' => 0,
                    'hours_total' => 0,
                    'jobs_total' => 0,
                ]);
            }
        }

        $shifts = Shift::where(function ($query) {
            $query->where('status', Shift::STATUS_FINISHED)
                ->orWhere(function($query) {
                    $query->where('status', Shift::STATUS_ACCEPTED_BY_PROVIDER)
                        ->where('processed', 1);
                });
        })
            ->whereHas('creator', function ($query) {
                $query->where('is_test_account', 0);
            })
            ->get();

        foreach ($shifts as $shift) {
            $practice = Practice::where('id', $shift->practice->id)->first();
            $practice->update([
                'hires_total' => $practice->hires_total + 1,
                'paid_total' => $practice->paid_total + $shift->cost_for_practice
            ]);
            $provider = Specialist::where('user_id', $shift->provider->user_id)->first();
            $provider->update([
                'paid_total' => $provider->paid_total + $shift->cost,
                'hours_total' => $provider->hours_total + round($shift->shift_time / 60),
                'jobs_total' => $provider->jobs_total + 1,
            ]);
        }
    }
}
