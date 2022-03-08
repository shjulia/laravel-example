<?php

declare(strict_types=1);

namespace App\Http\Controllers\Integration\Driver\DriverLicense;

use App\Http\Controllers\Controller;
use App\UseCases\Auth\Provider\IntegrationService;
use Illuminate\Http\Response;

class DriverLicenseController extends Controller
{
    /**
     * @var IntegrationService
     */
    private $integrationService;

    public function __construct(IntegrationService $integrationService)
    {
        $this->integrationService = $integrationService;
    }

    public function updateLicense(string $uuid)
    {
        $this->integrationService->updateDriver($uuid);
        return response()->json([], Response::HTTP_OK);
    }
}
