<?php

declare(strict_types=1);

use App\Entities\Shift\Shift;
use App\Entities\Shift\ShiftInvite;
use App\Entities\User\Practice\Practice;
use App\Entities\User\Provider\Specialist;
use App\Entities\User\User;
use App\UseCases\Shift\CostService;
use App\UseCases\Shift\ShiftPaymentService;
use Illuminate\Database\Seeder;

/**
 * Class MultiShiftSeeder
 */
class MultiShiftSeeder extends Seeder
{
    public function run(): void
    {
        $provider = Specialist::where('user_id', 686)->first();
        $practiceUser = User::where('id', 116)->first();
        /** @var Shift $shift */
        $shift = Shift::make([
            'position_id' => $provider->position_id,
            'practice_id' => $practiceUser->practice->id,
            'creator_id' => $practiceUser->id,
            'date' => "2020-02-17",
            'end_date' => "2020-05-15",
            'from_time' => "08:00",
            'to_time' => "17:00",
            'shift_time' => 9060,
            'lunch_break' => 60,
            'multi_days' => 20,
            'cost'=> 3624,
            'cost_for_practice' => 4167.6,
        ]);
        $shift->setAcceptedByProviderStatus();
        $shift->save();
        $shift->shiftInvites()->create([
            'provider_id' => $provider->user_id,
            'status' => ShiftInvite::ACCEPTED
        ]);
        foreach (self::DATES as $date) {
            /** @var Shift $child */
            $child = new Shift();
            $child->parent_shift_id = $shift->id;
            $child->practice_id = $shift->practice_id;
            $child->creator_id = $shift->creator_id;
            $child->provider_id = $provider->id;
            $child->setAcceptedByProviderStatus();
            $child->position_id = $shift->position_id;
            $child->date = $date['date'];
            $child->end_date = $date['date'];
            $child->from_time = $date['from_time'];
            $child->to_time = $date['to_time'];
            $child->shift_time = (strtotime($date['to_time']) - strtotime($date['from_time'])) / 60 - $shift->lunch_break;
            $child->cost = round($provider->position->fee * $child->shift_time / 60, 2);
            $child->cost_for_practice = round($child->cost * CostService::DEFAULT_TIER, 2);
            $child->lunch_break = $shift->lunch_break;
            $child->save();
        }
        $shiftPaymentService = app()->get(ShiftPaymentService::class);
        foreach ($shift->children as $child) {
            if ($child->date < "2020-03-01") {
                $shiftPaymentService->refundAndPay($child, $child->cost_for_practice);
            }
        }
    }

    const DATES = [
        [
            'date' => '2020-02-17',
            'end_date' => '2020-02-17',
            'from_time' => '08:00',
            'to_time' => '17:00'
        ],
        [
            'date' => '2020-02-21',
            'end_date' => '2020-02-21',
            'from_time' => '09:00',
            'to_time' => '17:00'
        ],
        [
            'date' => '2020-02-24',
            'end_date' => '2020-02-24',
            'from_time' => '08:00',
            'to_time' => '17:00'
        ],
        [
            'date' => '2020-03-02',
            'end_date' => '2020-03-02',
            'from_time' => '08:00',
            'to_time' => '17:00'
        ],
        [
            'date' => '2020-03-06',
            'end_date' => '2020-03-06',
            'from_time' => '09:00',
            'to_time' => '17:00'
        ],
        [
            'date' => '2020-03-09',
            'end_date' => '2020-03-09',
            'from_time' => '08:00',
            'to_time' => '17:00'
        ],
        [
            'date' => '2020-03-16',
            'end_date' => '2020-03-16',
            'from_time' => '08:00',
            'to_time' => '17:00'
        ],
        [
            'date' => '2020-03-13',
            'end_date' => '2020-03-13',
            'from_time' => '09:00',
            'to_time' => '17:00'
        ],
        [
            'date' => '2020-03-30',
            'end_date' => '2020-03-30',
            'from_time' => '08:00',
            'to_time' => '17:00'
        ],
        [
            'date' => '2020-04-03',
            'end_date' => '2020-04-03',
            'from_time' => '09:00',
            'to_time' => '17:00'
        ],
        [
            'date' => '2020-04-06',
            'end_date' => '2020-04-06',
            'from_time' => '08:00',
            'to_time' => '17:00'
        ],
        [
            'date' => '2020-04-13',
            'end_date' => '2020-04-13',
            'from_time' => '08:00',
            'to_time' => '17:00'
        ],
        [
            'date' => '2020-04-17',
            'end_date' => '2020-04-17',
            'from_time' => '09:00',
            'to_time' => '17:00'
        ],
        [
            'date' => '2020-04-20',
            'end_date' => '2020-04-20',
            'from_time' => '08:00',
            'to_time' => '17:00'
        ],
        [
            'date' => '2020-04-24',
            'end_date' => '2020-04-24',
            'from_time' => '09:00',
            'to_time' => '17:00'
        ],
        [
            'date' => '2020-05-01',
            'end_date' => '2020-05-01',
            'from_time' => '09:00',
            'to_time' => '17:00'
        ],
        [
            'date' => '2020-05-04',
            'end_date' => '2020-05-04',
            'from_time' => '08:00',
            'to_time' => '17:00'
        ],
        [
            'date' => '2020-05-08',
            'end_date' => '2020-05-08',
            'from_time' => '09:00',
            'to_time' => '17:00'
        ],
        [
            'date' => '2020-05-11',
            'end_date' => '2020-05-11',
            'from_time' => '08:00',
            'to_time' => '17:00'
        ],
        [
            'date' => '2020-05-15',
            'end_date' => '2020-05-15',
            'from_time' => '09:00',
            'to_time' => '17:00'
        ],
    ];
}
