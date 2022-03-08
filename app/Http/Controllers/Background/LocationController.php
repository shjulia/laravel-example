<?php

declare(strict_types=1);

namespace App\Http\Controllers\Background;

use App\Http\Controllers\Controller;
use App\UseCases\Background\Provider\ProviderService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

/**
 * Class LocationController
 * @package App\Http\Controllers\Background
 */
class LocationController extends Controller
{
    /**
     * @var ProviderService
     */
    private $providerService;

    /**
     * LocationController constructor.
     * @param ProviderService $providerService
     */
    public function __construct(ProviderService $providerService)
    {
        $this->providerService = $providerService;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateLocation(Request $request)
    {
        $user = Auth::user();
        if (!$user->isProvider()) {
            abort(403);
        }
        if (!session()->get('isAdmin', false)) {
            $this->providerService->markLastLocation($user, $request->lat, $request->lng);
        }
        return response()->json([], Response::HTTP_OK);
    }
}
