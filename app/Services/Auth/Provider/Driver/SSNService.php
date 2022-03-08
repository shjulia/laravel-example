<?php

declare(strict_types=1);

namespace App\Services\Auth\Provider\Driver;

use App\Services\Integration\CoreService;

class SSNService
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
     * @param string $uuid
     * @param string $ssn
     */
    public function setSSN(string $uuid, string $ssn): void
    {
        $this->core->request('POST', '/driver/set-ssn', [
            'id' => $uuid,
            'ssn' => $ssn
        ]);
    }
}
