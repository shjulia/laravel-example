<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Entities\User\User;
use App\Http\Controllers\Controller;
use App\UseCases\Auth\ActivationService;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class ActivationController extends Controller
{
    /**
     * @var ActivationService
     */
    private $activationService;

    /**
     * ActivationController constructor.
     * @param ActivationService $activationService
     */
    public function __construct(ActivationService $activationService)
    {
        $this->activationService = $activationService;
    }

    public function deactivate(User $user)
    {
        $currentUser = Auth::user();
        if (!Gate::allows('deactivate-user', $currentUser, $user)) {
            abort(Response::HTTP_FORBIDDEN);
        }
        try {
            $this->activationService->deactivate($user);
        } catch (\DomainException $e) {
            return back()->with(['error' => $e->getMessage()]);
        }
        return back()->with(['success' => 'Account was deactivated']);
    }

    public function activate(User $user)
    {
        $currentUser = Auth::user();
        if (!Gate::allows('deactivate-user', $currentUser, $user)) {
            abort(Response::HTTP_FORBIDDEN);
        }
        try {
            $this->activationService->activate($user);
        } catch (\DomainException $e) {
            return back()->with(['error' => $e->getMessage()]);
        }
        return back()->with(['success' => 'Account was activated']);
    }
}
