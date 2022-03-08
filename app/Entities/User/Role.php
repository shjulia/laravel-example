<?php

declare(strict_types=1);

namespace App\Entities\User;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Role - main user roles
 *
 * @package App\Entities\User
 * @property int $id
 * @property string $title
 * @property string $type
 * @mixin \Eloquent
 */
class Role extends Model
{
    /**
     * @var array
     */
    protected $guarded = [];

    /**
     * @var bool
     */
    public $timestamps = false;

    /** @var string */
    public const ROLE_SUPER_ADMIN = 'super_admin';
    /** @var string */
    public const ROLE_ADMIN = 'admin';
    /** @var string */
    public const ROLE_PROVIDER = 'provider';
    /** @var string */
    public const ROLE_PRACTICE = 'practice';
    /** @var string */
    public const ROLE_PARTNER = 'partner';
    /** @var string */
    public const ROLE_CUSTOMER_SUCCESS = 'customer_success';
    /** @var string */
    public const ROLE_ACCOUNTANT = 'accountant';

    /** @var string */
    public const PRACTICE_ADMINISTRATOR = 'practice_administrator';
    /** @var string */
    public const PRACTICE_HIRING_MANAGER = 'hiring_manager';
    /** @var string */
    public const PRACTICE_BILLING_MANAGER = 'billing_manager';

    /**
     * specific practice roles for user_practice table
     * @return array
     */
    public static function practiceRoles(): array
    {
        return [
          self::PRACTICE_ADMINISTRATOR => 'Practice Administrator',
          self::PRACTICE_HIRING_MANAGER => 'Practice Hiring Manager',
          self::PRACTICE_BILLING_MANAGER => 'Practice Billing Manager'
        ];
    }
}
