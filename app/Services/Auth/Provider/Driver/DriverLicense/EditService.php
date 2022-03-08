<?php

declare(strict_types=1);

namespace App\Services\Auth\Provider\Driver\DriverLicense;

use App\Entities\User\Provider\Specialist;
use App\Services\Integration\CoreService;

class EditService
{
    /**
     * @var CoreService
     */
    private $core;

    public function __construct(CoreService $core)
    {
        $this->core = $core;
    }

    /**
     * @param Specialist $provider
     * @return array
     */
    public function edit(Specialist $provider): array
    {
        return $this->core->request('POST', '/driver/edit', [
            'id' => $provider->user->uuid,
            'number' => $provider->driver_license_number,
            'address' => $provider->driver_address,
            'city' => $provider->driver_city,
            'state' => $provider->driver_state,
            'zip' => $provider->driver_zip,
            'first_name' => $provider->driver_first_name,
            'last_name' => $provider->driver_last_name,
            'middle_name' => $provider->driver_middle_name,
            'birth_date' => $provider->dob,
            'expiration_date' => $provider->driver_expiration_date->format('Y-m-d H:i:s'),
            'gender' => $provider->driver_gender
        ]);
    }
}
