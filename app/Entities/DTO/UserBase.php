<?php

declare(strict_types=1);

namespace App\Entities\DTO;

use App\Entities\User\Role;

/**
 * Class UserBase - DTO for transfer base user data
 * @package App\Entities\DTO
 */
class UserBase
{
    /**
     * @var string
     */
    public $first_name;
    /**
     * @var string
     */
    public $last_name;
    /**
     * @var string
     */
    public $email;
    /**
     * @var null|string
     */
    public $phone;
    /**
     * @var int|null
     */
    public $industry;
    /**
     * @var string
     */
    public $role = null;
    /**
     * @var null|string
     */
    public $password;
    /**
     * @var null|string
     */
    public $code;
    /**
     * @var null|float
     */
    public $lat = null;
    /**
     * @var null|float
     */
    public $lng = null;
    /**
     * @var string
     */
    public $uuid = '';

    /**
     * UserBase constructor.
     * @param string $firstName
     * @param string $lastName
     * @param string $email
     * @param null|string $phone
     * @param int|null $industry
     * @param null|string $password
     * @param null|string $code
     */
    public function __construct(
        string $firstName,
        string $lastName,
        string $email,
        ?string $phone,
        ?int $industry,
        ?string $password,
        ?string $code = null
    ) {
        $this->first_name = $firstName;
        $this->last_name = $lastName;
        $this->email = $email;
        $this->phone = $phone;
        $this->industry = $industry;
        $this->password = $password;
        $this->code = $code;
    }

    /**
     * @param string|null $lat
     * @param string|null $lng
     */
    public function viaLocation(?string $lat = null, ?string $lng = null): void
    {
        $this->lat = $lat;
        $this->lng = $lng;
    }

    public function toProvider(): void
    {
        $this->role = Role::ROLE_PROVIDER;
    }

    public function toPractice(): void
    {
        $this->role = Role::ROLE_PRACTICE;
    }

    /**
     * @return bool
     */
    public function isProvider(): bool
    {
        return $this->role == Role::ROLE_PROVIDER;
    }

    /**
     * @return bool
     */
    public function isPractice(): bool
    {
        return $this->role == Role::ROLE_PRACTICE;
    }
}
