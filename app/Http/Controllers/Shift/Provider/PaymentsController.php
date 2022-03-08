<?php

namespace App\Http\Controllers\Shift\Provider;

use App\Http\Controllers\Controller;
use App\Jobs\Shift\Provider\PaymentJob;
use App\Repositories\Payment\ProviderBonusesRepository;
use App\Repositories\Payment\ProviderChargeRepository;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

/**
 * Class PaymentsController
 * @package App\Http\Controllers\Shift\Provider
 */
class PaymentsController extends Controller
{
    /**
     * @var ProviderChargeRepository
     */
    private $providerChargeRepository;
    /**
     * @var ProviderBonusesRepository
     */
    private $providerBonusesRepository;

    /**
     * PaymentsController constructor.
     * @param ProviderChargeRepository $providerChargeRepository
     * @param ProviderBonusesRepository $providerBonusesRepository
     */
    public function __construct(
        ProviderChargeRepository $providerChargeRepository,
        ProviderBonusesRepository $providerBonusesRepository
    ) {
        $this->providerChargeRepository = $providerChargeRepository;
        $this->providerBonusesRepository = $providerBonusesRepository;
    }

    /**
     * Withdrawal of user funds
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function withdraw()
    {
        $user = Auth::user();
        try {
            PaymentJob::dispatch($user, null, true, true);
        } catch (\DomainException $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_BAD_GATEWAY);
        }

        return response()->json(['success' => 'success'], Response::HTTP_OK);
    }
}
