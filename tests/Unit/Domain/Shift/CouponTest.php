<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Shift;

use App\Entities\Shift\Coupon;
use Tests\Builders\Data\PositionBuilder;
use Tests\Builders\Data\StateBuilder;
use Tests\TestCase;

/**
 * Class CouponTest
 * @package Tests\Unit\Domain\Shift
 */
class CouponTest extends TestCase
{
    public function testCreation()
    {
        $coupon = Coupon::createBase(
            $code = str_random(16),
            $start = new \DateTimeImmutable(),
            $start->add(new \DateInterval('P7D')),
            $percent = 15.0,
            null,
            $accLimit = 1,
            $globalLimit = 5,
            $minBill = 100.0
        );
        $this->assertNotNull($coupon);
        $this->assertEquals($percent, $coupon->percent_off);
        $this->assertNull($coupon->dollar_off);
        $this->assertEquals($accLimit, $coupon->use_per_account_limit);
        $this->assertEquals($globalLimit, $coupon->use_globally_limit);
        $this->assertEquals($minBill, $coupon->minimum_bill);
        $this->assertNull($coupon->practice_id);
        $this->assertIsString($coupon->start_date);
        $this->assertIsString($coupon->end_date);
        $this->assertLessThan($coupon->end_date, $coupon->start_date);
    }

    public function testEdit()
    {
        $coupon = Coupon::createBase(
            $code = str_random(16),
            $start = new \DateTimeImmutable(),
            $start->add(new \DateInterval('P7D')),
            $percent = 15.0,
            null,
            $accLimit = 1,
            $globalLimit = 5,
            null
        );
        $coupon->editTime(
            $newStart = new \DateTimeImmutable(),
            $newEnd = $start->add(new \DateInterval('P7D'))
        );
        $this->assertEquals($newStart->format('Y-m-d'), $coupon->start_date);
        $this->assertEquals($newEnd->format('Y-m-d'), $coupon->end_date);

        $coupon->edit(
            $percent = null,
            $dollars = 50.0,
            $accLimit = 2,
            $globalLimit = 7,
            $minBill = 100.0
        );
        $this->assertNull($coupon->percent_off);
        $this->assertEquals($dollars, $coupon->dollar_off);
        $this->assertEquals($accLimit, $coupon->use_per_account_limit);
        $this->assertEquals($globalLimit, $coupon->use_globally_limit);
        $this->assertEquals($minBill, $coupon->minimum_bill);
    }

    public function testValid()
    {
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
        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('Coupon code have been expired by usages.');
        $coupon->checkValidByGloballyLimit($globalLimit);

        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('Coupon code have been expired by usages.');
        $coupon->checkValidByGloballyLimit($accLimit);

        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('Minimum bill for this coupon should be $' . $minBill);
        $coupon->checkValidByBill(50.0);

        $coupon->checkValidByTime();
        $coupon->editTime(
            $date->add(new \DateInterval('P1D')),
            $date->add(new \DateInterval('P8D'))
        );

        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('Coupon will be valid from' . $coupon->start_date);
        $coupon->checkValidByTime();

        $coupon->editTime(
            $date->sub(new \DateInterval('P7D')),
            $date->sub(new \DateInterval('P4D'))
        );
        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('Coupon has expired');
        $coupon->checkValidByTime();
    }

    public function testValidByPositions()
    {
        $date = new \DateTimeImmutable();

        $coupon = Coupon::createBase(
            $code = str_random(16),
            $start = $date,
            $start->add(new \DateInterval('P7D')),
            $percent = 15.0,
            null
        );
        $position1 = (new PositionBuilder())->build();
        $position2 = (new PositionBuilder())->build();
        $coupon->positions = collect([$position1, $position2]);
        $coupon->checkValidByPosition($position1->id);
        $coupon->checkValidByPosition($position2->id);
        $position3 = (new PositionBuilder())->build();
        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('This Coupon code is not valid for selected position.');
        $coupon->checkValidByPosition($position3->id);
    }

    public function testValidByStates()
    {
        $date = new \DateTimeImmutable();

        $coupon = Coupon::createBase(
            $code = str_random(16),
            $start = $date,
            $start->add(new \DateInterval('P7D')),
            $percent = 15.0,
            null
        );
        $state1 = (new StateBuilder())->build();
        $state2 = (new StateBuilder())->build();
        $coupon->states = collect([$state1, $state2]);
        $coupon->checkValidByState($state1->short_title);
        $coupon->checkValidByState($state2->short_title);
        $state3 = (new StateBuilder())->build();
        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('This Coupon code is not valid for your state.');
        $coupon->checkValidByState($state3->short_title);
    }
}
