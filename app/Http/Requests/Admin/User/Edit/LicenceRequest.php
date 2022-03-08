<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin\User\Edit;

use App\Entities\User\User;
use App\Http\Requests\Auth\Provider\LicenceRequest as ProviderLicenceRequest;

/**
 * Class LicenceRequest
 * @package App\Http\Requests\Admin\User\Edit
 */
class LicenceRequest extends ProviderLicenceRequest
{
    /**
     * @return User
     */
    private function getUserByParams(): User
    {
        return User::where('id', $this->user_id)->with('specialist.licenses')->first();
    }
}
