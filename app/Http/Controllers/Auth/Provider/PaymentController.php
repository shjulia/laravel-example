<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth\Provider;

use App\Entities\User\User;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\Provider\Onboarding\PaymentRequest;
use App\UseCases\Auth\Provider\DetailsService;
use Illuminate\Support\Facades\Auth;

/**
 * Class PaymentController
 * @package App\Http\Controllers\Auth\Provider
 */
class PaymentController extends Controller
{
    /**
     * @var DetailsService
     */
    private $service;

    /**
     * PaymentController constructor.
     * @param DetailsService $service
     */
    public function __construct(DetailsService $service)
    {
        $this->service = $service;
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getPaid()
    {
        /** @var User $user */
        $user = Auth::user();
        return view('account.provider.edit.get-paid', compact('user'));
    }

    /**
     * @param PaymentRequest $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function getPaidSave(PaymentRequest $request)
    {
        /** @var User $user */
        $user = Auth::user();
        try {
            $this->service->setPayment($user, $request);
        } catch (\DomainException $e) {
            return back()->with(['error' => $e->getMessage()]);
        }
        return redirect()->route('home');
    }
}
