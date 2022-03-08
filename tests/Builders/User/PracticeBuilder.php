<?php

declare(strict_types=1);

namespace Tests\Builders\User;

use App\Entities\User\Practice\Practice;
use Faker\Factory;
use Tests\Builders\Data\IndustryBuilder;

/**
 * Class PracticeBuilder
 * @package Tests\Builders\User
 */
class PracticeBuilder
{
    /**
     * @var Practice
     */
    private $practice;

    public function __construct()
    {
        $industry = (new IndustryBuilder())->build();
        $this->practice = Practice::createBase($industry);
        $this->practice->id = mt_rand();
        $this->faker = Factory::create();
    }

    public function active(): self
    {
        $clone = $this;
        $clone->practice->changeApprovalStatus();
        return $clone;
    }

    public function withBase(): self
    {
        $clone = $this;
        $clone->practice->setBaseInfo(
            $this->faker->title,
            $this->faker->address,
            $this->faker->city,
            'NC',
            (string)mt_rand(),
            $this->faker->url,
            $this->faker->phoneNumber
        );
        return $clone;
    }

    public function build(): Practice
    {
        return $this->practice;
    }
}
