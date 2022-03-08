<?php

declare(strict_types=1);

namespace App\Http\Controllers\Integration\Driver\Checkr;

use App\Http\Controllers\Controller;
use App\UseCases\Auth\Provider\IntegrationService;
use Illuminate\Http\Response;

class CheckrController extends Controller
{
    /**
     * @var IntegrationService
     */
    private $integrationService;

    public function __construct(IntegrationService $integrationService)
    {
        $this->integrationService = $integrationService;
    }

    public function update(string $uuid)
    {
        $this->integrationService->updateCheckr($uuid);
        return response()->json([], Response::HTTP_OK);
    }
}
