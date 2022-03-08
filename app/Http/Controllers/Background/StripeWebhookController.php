<?php

declare(strict_types=1);

namespace App\Http\Controllers\Background;

use App\Http\Controllers\Controller;
use App\Jobs\Shift\Provider\ChangeBalanceJob;
use App\Repositories\User\UserRepository;
use App\UseCases\Shift\ShiftPaymentService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * Class StripeWebhookController
 * @package App\Http\Controllers\Background
 */
class StripeWebhookController extends Controller
{
    /**
     * @var ShiftPaymentService
     */
    private $shiftPaymentService;

    public function __construct(ShiftPaymentService $shiftPaymentService)
    {
        $this->shiftPaymentService = $shiftPaymentService;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function chargeStatus(Request $request)
    {
        try {
            $this->shiftPaymentService->handleStatus(
                $request->id,
                $request->status,
                $request->is_captured,
                $request->refund['refund_amount']
            );
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_BAD_GATEWAY);
        }

        return response()->json([], Response::HTTP_OK);
    }

    /**
     * @param Request $request
     * @param UserRepository $users
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateBalance(Request $request, UserRepository $users)
    {
        $user = $users->getByWalletClientId($request->id);
        ChangeBalanceJob::dispatch($user);
        return response()->json([], Response::HTTP_OK);
    }
}
