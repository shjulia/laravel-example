<?php

namespace App\Repositories\User;

use App\Entities\User\Role;

/**
 * Class RolesRepository
 * @package App\Repositories\User
 */
class RolesRepository
{
    /**
     * @return Role
     */
    public function getProviderRole()
    {
        if (!$role = Role::where('type', Role::ROLE_PROVIDER)->first()) {
            throw new \DomainException('Role not found');
        }
        return $role;
    }

    /**
     * @return Role
     */
    public function getPartnerRole()
    {
        if (!$role = Role::where('type', Role::ROLE_PARTNER)->first()) {
            throw new \DomainException('Role not found');
        }
        return $role;
    }

    /**
     * @return Role
     */
    public function getPracticeRole()
    {
        if (!$role = Role::where('type', Role::ROLE_PRACTICE)->first()) {
            throw new \DomainException('Role not found');
        }
        return $role;
    }

    public function getById(int $id): Role
    {
        return Role::where('id', $id)->firstOrFail();
    }

    /**
     * @return Role[]
     */
    public function findWorkRoles()
    {
        return Role::whereIn('type', [Role::ROLE_PRACTICE, Role::ROLE_PROVIDER])->get();
    }

    /**
     * @return Role[]
     */
    public function findAll()
    {
        return Role::get();
    }
}
