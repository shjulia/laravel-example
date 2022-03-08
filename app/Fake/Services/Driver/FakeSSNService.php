<?php

declare(strict_types=1);

namespace App\Fake\Services\Driver;

use App\Services\Auth\Provider\Driver\SSNService;

class FakeSSNService extends SSNService
{
    public function setSSN(string $uuid, string $ssn): void
    {
    }
}
