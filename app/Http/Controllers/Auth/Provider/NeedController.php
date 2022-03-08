<?php

namespace App\Http\Controllers\Auth\Provider;

use App\Entities\User\User;
use App\Http\Controllers\Controller;
use App\Repositories\User\UserRepository;

/**
 * Class NeedController
 * @package App\Http\Controllers\Auth\Provider
 */
class NeedController extends Controller
{
    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @var User
     */
    private $user;

    /**
     * NeedController constructor.
     * @param UserRepository $userRepository
     */
    public function __construct(UserRepository $userRepository)
    {
        $this->middleware(['guest', function ($request, $next) {
            try {
                $this->user = $this->userRepository->getProviderByTmpCode($request->code);
                return $next($request);
            } catch (\DomainException $e) {
                return back()->with(['error' => $e->getMessage()]);
            }
        }]);
        $this->userRepository = $userRepository;
    }

    /**
     * @param string $code
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function need(string $code)
    {
        if (!$this->user->specialist || $this->user->isSignupFinished()) {
            abort(403);
        }
        $user = $this->user;
        $routeBase = 'signup.' . explode(':', $this->user->signup_step)[1] ?? 'identity';
        return view('auth.need', compact('user', 'routeBase'));
    }
}
