<?php

declare(strict_types=1);

namespace App\Fake\Services\Driver;

use App\Entities\DTO\UserBase;
use App\Services\Auth\Provider\Driver\CreateService;

class FakeCreateService extends CreateService
{
    public function createDriver(UserBase $command): void
    {
    }

    public function createDriverFull(array $data): void
    {
    }
}
