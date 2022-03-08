<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\Users\Provider;

use App\Entities\User\License;
use App\Http\Controllers\Controller;
use App\UseCases\Admin\Manage\Users\LicenseService;
use Illuminate\Http\Request;

/**
 * Class LicenseController
 * @package App\Http\Controllers\Admin\Users\Provider
 */
class LicenseController extends Controller
{
    /**
     * @var LicenseService
     */
    private $licenseService;

    /**
     * LicenseController constructor.
     * @param LicenseService $licenseService
     */
    public function __construct(LicenseService $licenseService)
    {
        $this->licenseService = $licenseService;
    }

    /**
     * @param License $license
     * @return \Illuminate\Http\RedirectResponse
     */
    public function approve(License $license)
    {
        try {
            $this->licenseService->approve($license);
        } catch (\DomainException $e) {
            return back()->with(['error' => $e->getMessage()]);
        }
        return back()->with(['success' => 'License has been updated successfully']);
    }

    /**
     * @param License $license
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function decline(License $license, Request $request)
    {
        try {
            $this->licenseService->decline($license, $request->reason);
        } catch (\DomainException $e) {
            return back()->with(['error' => $e->getMessage()]);
        }
        return back()->with(['success' => 'License has been updated successfully']);
    }

    /**
     * @param License $license
     * @return \Illuminate\Http\RedirectResponse
     */
    public function setBaseStatus(License $license)
    {
        try {
            $this->licenseService->setBaseStatus($license);
        } catch (\DomainException $e) {
            return back()->with(['error' => $e->getMessage()]);
        }
        return back()->with(['success' => 'License has been updated successfully']);
    }
}
