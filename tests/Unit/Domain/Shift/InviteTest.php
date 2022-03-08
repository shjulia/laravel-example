<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Shift;

use App\Entities\Shift\ShiftInvite;
use Tests\Builders\Shift\ShiftBuilder;
use Tests\Builders\User\ProviderBuilder;
use Tests\TestCase;

/**
 * Class InviteTest
 * @package Tests\Unit\Domain\Shift
 */
class InviteTest extends TestCase
{
    public function testNewInvite()
    {
        $shift = (new ShiftBuilder())
            ->withActiveDateTimeValues()
            ->withCosts()
            ->withMatchingStatus()
            ->build();
        $provider = (new ProviderBuilder())->active()->build();

        $invite = ShiftInvite::newInvite($shift, $provider);
        $this->assertEquals($provider->id, $invite->provider_id);
        $this->assertEquals($shift->id, $invite->shift_id);
        $this->assertTrue($invite->isNoRespond());
    }

    public function testNewFailedInvite()
    {
        $shift = (new ShiftBuilder())
            ->withActiveDateTimeValues()
            ->withCosts()
            ->build();
        $provider = (new ProviderBuilder())->active()->build();
        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('Shift doesn\'t have matching status now');
        ShiftInvite::newInvite($shift, $provider);
    }

    public function testAcceptInvite()
    {
        $shift = (new ShiftBuilder())
            ->withActiveDateTimeValues()
            ->withCosts()
            ->withMatchingStatus()
            ->build();
        $provider = (new ProviderBuilder())->active()->build();

        $invite = ShiftInvite::newInvite($shift, $provider);
        $invite->accept();
        $this->assertTrue($invite->isAccepted());
    }

    public function testDeclineInvite()
    {
        $shift = (new ShiftBuilder())
            ->withActiveDateTimeValues()
            ->withCosts()
            ->withMatchingStatus()
            ->build();
        $provider = (new ProviderBuilder())->active()->build();

        $invite = ShiftInvite::newInvite($shift, $provider);
        $invite->decline();
        $this->assertTrue($invite->isDeclined());
    }

    public function testViewInvite()
    {
        $shift = (new ShiftBuilder())
            ->withActiveDateTimeValues()
            ->withCosts()
            ->withMatchingStatus()
            ->build();
        $provider = (new ProviderBuilder())->active()->build();

        $invite = ShiftInvite::newInvite($shift, $provider);
        $invite->setViewedStatus();
        $this->assertTrue($invite->isViewed());
    }
}
