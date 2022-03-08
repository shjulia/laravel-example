<?php

declare(strict_types=1);

namespace App\UseCases\Auth\Provider;

use App\Repositories\User\SpecialistRepository;
use App\Services\Integration\CoreService;
use Carbon\Carbon;

class IntegrationService
{
    /**
     * @var SpecialistRepository
     */
    private $specialists;
    /**
     * @var CoreService
     */
    private $core;

    public function __construct(SpecialistRepository $specialists, CoreService $core)
    {
        $this->specialists = $specialists;
        $this->core = $core;
    }

    public function updateDriver(string $uuid): void
    {
        $provider = $this->specialists->getByUuid($uuid);
        $data = $this->core->fetch('GET', '/driver/' . $provider->user->uuid . '/view');
        $provider->update([
            'driver_license_number' => $data['number'],
            'driver_address' => $data['address']['address'],
            'driver_city' => $data['address']['city'],
            'driver_state' => $data['address']['state'],
            'driver_zip' => $data['address']['zip'] ,
            'driver_first_name' => $data['name']['first'],
            'driver_last_name' => $data['name']['last'],
            'driver_middle_name' => $data['name']['middle'],
            'dob' => $data['birthDate'] ? Carbon::parse($data['birthDate'])->format('Y-m-d') : null,
            'driver_expiration_date' => $data['expirationDate']
                ? Carbon::parse($data['expirationDate'])->format('Y-m-d')
                : null,
            'driver_gender' => $data['gender']
        ]);
    }

    public function updateCheckr(string $uuid): void
    {
        $provider = $this->specialists->getByUuid($uuid);
        $data = $this->core->fetch('GET', '/driver/' . $provider->user->uuid . '/checkr');
        $provider->checkr()->update([
            'checkr_status' => $data['status'],
            'checkr_candidate_id' => $data['candidateId'],
            'checkr_report_id' => $data['reportId']
        ]);
    }
}
