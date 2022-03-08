<?php

declare(strict_types=1);

namespace Tests\Builders\User;

use App\Entities\User\User;
use Faker\Factory;

/**
 * Class UserBuilder
 * @package Tests\Builders\User
 */
class UserBuilder
{
    /**
     * @var User
     */
    private $user;

    public function __construct()
    {
        $faker = Factory::create();
        $this->user = User::createBySignUp(
            $faker->safeEmail,
            $faker->firstName,
            $faker->lastName,
            'provider:industry'
        );
        $this->user->id = mt_rand();
    }

    /**
     * @return User
     */
    public function build()
    {
        return $this->user;
    }
}
