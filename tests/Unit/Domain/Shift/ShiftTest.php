<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Shift;

use App\Entities\Shift\Coupon;
use App\Entities\Shift\Shift;
use Carbon\Carbon;
use Tests\Builders\Data\PositionBuilder;
use Tests\Builders\Shift\ShiftBuilder;
use Tests\Builders\User\PracticeBuilder;
use Tests\Builders\User\ProviderBuilder;
use Tests\Builders\User\UserBuilder;
use Tests\TestCase;

/**
 * Class ShiftTest
 * @package Tests\Unit\Domain\Shift
 */
class ShiftTest extends TestCase
{
    public function testShiftBaseCreation()
    {
        $position = (new PositionBuilder())->build();
        $user = (new UserBuilder())->build();
        $practice = (new PracticeBuilder())->withBase()->active()->build();
        $shift = Shift::createBase($position, $user, $practice);
        $this->assertEquals($position->id, $shift->position_id);
        $this->assertEquals($practice->id, $shift->practice_id);
        $this->assertEquals($user->id, $shift->creator_id);
        $this->assertTrue($shift->isCreatingStatus());
        $position = (new PositionBuilder())->build();
        $shift->editPosition($position);
        $this->assertEquals($position->id, $shift->position_id);
    }

    public function testShiftTimeCreation()
    {
        $shift = (new ShiftBuilder())->build();
        $now = Carbon::now();
        $shift->editDateTimeValues(
            $dateFrom = $now->format('Y-m-d'),
            $dateTo = $now->addHour(7)->format('Y-m-d'),
            $timeFrom = $now->addHour(1)->format('H:i'),
            $timeTo = $now->addHour(7)->format('H:i'),
            $shiftTIme = 360,
            1,
            $lunchBreak = 0
        );
        $this->assertEquals($dateFrom, $shift->date);
        $this->assertEquals($dateTo, $shift->end_date);
        $this->assertEquals($timeFrom, $shift->from_time);
        $this->assertEquals($timeTo, $shift->to_time);
        $this->assertEquals(0, $shift->multi_days);
        $this->assertEquals($lunchBreak, $shift->lunch_break);

        $shift->editDateTimeValues(
            $dateFrom = $now->format('Y-m-d'),
            $dateTo = $now->addHour(7)->format('Y-m-d'),
            $timeFrom = $now->addHour(1)->format('H:i'),
            $timeTo = $now->addHour(7)->format('H:i'),
            $shiftTIme = 360,
            $multiDays = 2
        );
        $this->assertEquals($multiDays, $shift->multi_days);
        $this->assertEquals(0, $shift->lunch_break);
    }

    public function testCostsSettingUp()
    {
        $shift = (new ShiftBuilder())->withActiveDateTimeValues()->build();
        $shift->editCosts($costFroProvider = 1000, $costForPractice = 1200);
        $this->assertEquals($costFroProvider, $shift->cost);
        $this->assertEquals($costForPractice, $shift->cost_for_practice);
    }

    public function testAssignProvider()
    {
        $shift = (new ShiftBuilder())
            ->withActiveDateTimeValues()
            ->withMatchingStatus()
            ->build();
        $this->assertTrue($shift->isMatchingStatus());
        $provider = (new ProviderBuilder())->active()->build();
        $shift->assignProviderToShift($provider);
        $this->assertTrue($shift->isHasProvider());
        $this->assertTrue($shift->isAcceptedByProviderStatus());
        $this->assertTrue($shift->isAcceptedByProviderStatus());
    }

    public function testApplyingCouponWithPercent()
    {
        $shift = (new ShiftBuilder())
            ->withActiveDateTimeValues()
            ->withMatchingStatus()
            ->build();
        $shift->editCosts(
            $costForProvider = 1000,
            $costForPractice = 1200
        );
        $date = new \DateTimeImmutable();
        $coupon = Coupon::createBase(
            $code = str_random(16),
            $start = $date,
            $start->add(new \DateInterval('P7D')),
            $percent = 15.0,
            null,
            $accLimit = 1,
            $globalLimit = 5,
            $minBill = 100.0
        );
        $coupon->id = mt_rand();
        $shift->applyCoupon($coupon);
        $this->assertNotNull($shift->coupon_id);
        $this->assertEquals($shift->coupon_id, $coupon->id);
        $this->assertEquals($costForProvider, $shift->cost);
        $this->assertEquals(round($costForPractice * (1 - $coupon->percent_off / 100), 2), $shift->cost_for_practice);
    }

    public function testApplyingCouponWithDollars()
    {
        $shift = (new ShiftBuilder())
            ->withActiveDateTimeValues()
            ->withMatchingStatus()
            ->build();
        $shift->editCosts(
            $costForProvider = 1000,
            $costForPractice = 1200
        );
        $date = new \DateTimeImmutable();
        $coupon = Coupon::createBase(
            $code = str_random(16),
            $start = $date,
            $start->add(new \DateInterval('P7D')),
            null,
            $dollarsOff = 10.0,
            $accLimit = 1,
            $globalLimit = 5,
            $minBill = 100.0
        );
        $coupon->id = mt_rand();
        $shift->applyCoupon($coupon);
        $this->assertEquals($costForProvider, $shift->cost);
        $this->assertEquals($costForPractice - $dollarsOff, $shift->cost_for_practice);
    }

    public function testChildrenCreation()
    {
        $shift = (new ShiftBuilder())
            ->withActiveDateTimeValues(3)
            ->withCosts()
            ->withMatchingStatus()
            ->build();
        $child = Shift::copyParentToChild($shift, $shift->date);
        $this->assertNotNull($child);
        $this->assertNotNull($child->parent_shift_id);
        $this->assertTrue($child->isParentMatchingStatus());
        $this->assertEquals($shift->position_id, $child->position_id);
        $this->assertEquals($shift->practice_id, $child->practice_id);
        $this->assertEquals($shift->creator_id, $child->creator_id);
        $this->assertEquals($shift->date, $child->date);
        $this->assertEquals($shift->date, $child->end_date);
        $this->assertEquals($shift->from_time, $child->from_time);
        $this->assertEquals($shift->to_time, $child->to_time);
        $this->assertEquals(0, $child->multi_days);
        $this->assertEquals($shift->lunch_break, $child->lunch_break);
    }
}
