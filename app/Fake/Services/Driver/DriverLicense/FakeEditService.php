<?php

declare(strict_types=1);

namespace App\Fake\Services\Driver\DriverLicense;

use App\Entities\User\Provider\Specialist;
use App\Services\Auth\Provider\Driver\DriverLicense\EditService;

class FakeEditService extends EditService
{
    public function edit(Specialist $provider): array
    {
        return [];
    }
}
