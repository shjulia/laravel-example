<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth\Provider;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\Provider\IdentityRequest;
use App\Http\Requests\Auth\Provider\UploadImageRequest;
use App\UseCases\Auth\Provider\RegisterService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

/**
 * Class EditController
 * @package App\Http\Controllers\Auth\Provider
 */
class EditController extends Controller
{
    /**
     * @var RegisterService
     */
    private $service;

    /**
     * EditController constructor.
     * @param RegisterService $service
     */
    public function __construct(RegisterService $service)
    {
        $this->middleware(['auth', 'can:provider-account-details']);
        $this->service = $service;
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function identity()
    {
        $user = Auth::user();
        return view('account.provider.edit.identity', compact('user'));
    }

    /**
     * @param IdentityRequest $request
     * @param RegisterService $service
     * @return \Illuminate\Http\RedirectResponse
     */
    public function identitySave(IdentityRequest $request)
    {
        $user = Auth::user();
        try {
            $this->service->identitySave($request, $user, false);
        } catch (\DomainException $e) {
            return back()->with(['error' => $e->getMessage()]);
        }

        return redirect()->route('dashboard')->with(['success' => 'Identity changes successfully']);
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    public function identityDelete()
    {
        $user = Auth::user();
        try {
            $this->service->identityRemove($user);
        } catch (\DomainException $e) {
            return back()->with(['error' => $e->getMessage()]);
        }
        return back()->with(['success' => 'Identity changes successfully']);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function phone(Request $request)
    {
        $user = Auth::user();
        try {
            if ($user->phone !== $request->phone) {
                $this->service->setPhone($request->phone, $user);
            }
        } catch (\DomainException $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_BAD_GATEWAY);
        }
        return response()->json([], Response::HTTP_OK);
    }

    /**
     * @param UploadImageRequest $request
     * @return array
     * @throws \Exception
     */
    public function uploadDriverLicense(UploadImageRequest $request)
    {
        $user = Auth::user();
        $path = $this->service->uploadDriverLicense($request, $user);
        $result = $this->service->analyzeImage($path, $user);

        return response()->json($result, Response::HTTP_OK);
    }
}
