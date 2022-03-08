<?php

declare(strict_types=1);

namespace App\Services\Auth\Provider\Driver\DriverLicense\Photo;

use App\Services\Integration\CoreService;

/**
 * Class AddService
 * @package App\Model\Ticket\Service\Demand\DriverLicense\Photo
 */
class AddService
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
     * @param string $id
     * @param string $url
     * @return array
     */
    public function add(string $id, string $url): array
    {
        return $this->core->request('POST', '/driver/analyze-uploaded-dl', [
            'id' => $id,
            'url' => $url,
        ]);
    }
}
