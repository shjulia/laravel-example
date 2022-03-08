<?php

declare(strict_types=1);

namespace App\Console\Commands\Driver;

use App\Entities\User\Provider\Checkr;
use App\Entities\User\User;
use App\Helpers\EncryptHelper;
use App\Repositories\User\SpecialistRepository;
use App\Services\Auth\Provider\Driver\CreateService;
use Illuminate\Console\Command;
use Ramsey\Uuid\Uuid;

class DriversToCoreCommand extends Command
{
    protected $signature = 'drivers:to-core';
    /**
     * @var SpecialistRepository
     */
    private $providers;
    /**
     * @var CreateService
     */
    private $createService;

    public function __construct(SpecialistRepository $providers, CreateService $createService)
    {
        parent::__construct();
        $this->providers = $providers;
        $this->createService = $createService;
    }

    public function handle()
    {
        $page = 1;
        while ($providers = $this->providers->findPaginate($page)) {
            if ($providers->isEmpty()) {
                return;
            }
            foreach ($providers as $provider) {
                if ($provider->isDuplicate()) {
                    continue;
                }
                /** @var User $user */
                $user = $provider->user;
                if (!$user->uuid) {
                    $user->update([
                        'uuid' => Uuid::uuid4()->toString()
                    ]);
                }
                $checkr = $provider->checkr;
                $data = [
                    'id' => $user->uuid,
                    'first_name' => $user->first_name,
                    'last_name' => $user->last_name,
                    'email' => $user->email,
                    'date' => $user->created_at->format('Y-m-d H:i:s'),
                    'ssn' => $provider->ssn ? EncryptHelper::decrypt($provider->ssn) : null,
                    'dl' => [
                        'photo' => $provider->driver_photo
                            ? 'https://s3.amazonaws.com/boonb/' . $provider->driver_photo
                            : null,
                        'number' => $provider->driver_license_number,
                        'address' => $provider->driver_address,
                        'city' => $provider->driver_city,
                        'state' => $provider->driver_state,
                        'zip' => $provider->driver_zip,
                        'first_name' => $provider->driver_first_name ?: $user->first_name,
                        'last_name' => $provider->driver_last_name ?: $user->last_name,
                        'middle_name' => $provider->driver_middle_name,
                        'birth_date' => $provider->dob,
                        'expiration_date' => $provider->driver_expiration_date
                            ? $provider->driver_expiration_date->format('Y-m-d H:i:s')
                            : null,
                        'gender' => $provider->driver_gender,
                        'is_approved' => (bool)$provider->isApproved()
                    ],
                    'checkr' => $checkr ? [
                        'status' => $checkr->checkr_status,
                        'candidate_id' => $checkr->checkr_candidate_id,
                        'report_id' => $checkr->checkr_report_id,
                        'error_response' => $checkr->checkr_error_response
                    ] : null
                ];
                try {
                    $this->createService->createDriverFull($data);
                } catch (\DomainException $e) {
                    if ($e->getMessage() === 'Driver is already exists') {
                        continue;
                    }
                    throw $e;
                }
                echo $data['id'] . PHP_EOL;
            }
            $page++;
        }
    }
}
