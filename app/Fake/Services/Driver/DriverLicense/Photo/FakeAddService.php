<?php

declare(strict_types=1);

namespace App\Fake\Services\Driver\DriverLicense\Photo;

use App\Services\Auth\Provider\Driver\DriverLicense\Photo\AddService;

class FakeAddService extends AddService
{
    public function add(string $id, string $url): array
    {
        return [];
    }
}
