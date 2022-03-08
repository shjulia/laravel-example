<?php

declare(strict_types=1);

namespace Tests\Builders\Shift;

use App\Entities\Shift\Shift;
use App\Entities\User\Practice\Practice;
use App\Entities\User\Provider\Specialist;
use App\Entities\User\User;
use Carbon\Carbon;
use Tests\Builders\Data\PositionBuilder;
use Tests\Builders\User\PracticeBuilder;
use Tests\Builders\User\ProviderBuilder;
use Tests\Builders\User\UserBuilder;

/**
 * Class ShiftBuilder
 * @package Tests\Builders\Shift
 */
class ShiftBuilder
{
    private $shift;

    public function __construct()
    {
        $position = (new PositionBuilder())->build();
        $user = (new UserBuilder())->build();
        $practice = (new PracticeBuilder())->withBase()->active()->build();
        $this->shift = Shift::createBase($position, $user, $practice);
        $this->shift->id = mt_rand();
    }

    public function withActiveDateTimeValues(?int $multiDays = 0): self
    {
        $clone = clone $this;
        $now = Carbon::now();
        $clone->shift->editDateTimeValues(
            $now->format('Y-m-d'),
            $multiDays
                ? $now->addDay($multiDays)->format('Y-m-d')
                : $now->addHour(7)->format('Y-m-d'),
            $now->addHour(1)->format('H:i'),
            $now->addHour(7)->format('H:i'),
            360,
            $multiDays,
            0
        );
        return $clone;
    }

    public function withCosts(): self
    {
        $clone = clone $this;
        $clone->shift->editCosts(1000, 1200);
        return $clone;
    }

    public function withAssignedProvider(): self
    {
        $clone = clone $this;
        $provider = (new ProviderBuilder())->active()->build();
        $clone->shift->assignProviderToShift($provider);
        return $clone;
    }

    public function withMatchingStatus(): self
    {
        $clone = clone $this;
        $clone->shift->setMatchingStatus();
        return $clone;
    }

    /**
     * @return Shift
     */
    public function build(): Shift
    {
        return $this->shift;
    }
}
