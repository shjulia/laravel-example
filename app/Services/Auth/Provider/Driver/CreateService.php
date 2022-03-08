<?php

declare(strict_types=1);

namespace App\Services\Auth\Provider\Driver;

use App\Entities\DTO\UserBase;
use App\Services\Integration\CoreService;

/**
 * Class CreateService
 * @package App\Model\Ticket\Service\Demand\Driver
 */
class CreateService
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
     * @param UserBase $command
     */
    public function createDriver(UserBase $command): void
    {
        $this->core->request('POST', '/driver/create', [
            'id' => $command->uuid,
            'projectName' => 'Boon Dental',
            'firstName' => $command->first_name,
            'lastName' => $command->last_name,
            'middleName' => null,
            'email' => $command->email,
            'date' => (new \DateTimeImmutable())->format('Y-m-d H:i:s')
        ]);
    }

    public function createDriverFull(array $data): void
    {
        $this->core->request('POST', '/driver/full-create', $data);
    }
}
