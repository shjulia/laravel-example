<?php

declare(strict_types=1);

namespace Tests\Builders\User;

use App\Entities\User\Provider\Specialist;
use Tests\Builders\Data\IndustryBuilder;

/**
 * Class ProviderBuilder
 * @package Tests\Builders\User
 */
class ProviderBuilder
{
    /**
     * @var Specialist
     */
    private $provider;

    public function __construct()
    {
        $industry = (new IndustryBuilder())->build();
        //$position = (new PositionBuilder($industry))->build();
        $user = (new UserBuilder())->build();
        $this->provider = Specialist::createBase($user, $industry);
    }

    public function active(): self
    {
        $clone = clone $this;
        $clone->provider->changeApprovalStatus();
        return $clone;
    }

    public function build(): Specialist
    {
        return $this->provider;
    }
}
