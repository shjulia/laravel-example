<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\Analytics;

use App\Http\Controllers\Controller;
use App\Repositories\User\LoginLogRepository;

class LoginLogController extends Controller
{
    /**
     * @var LoginLogRepository
     */
    private $logRepository;

    /**
     * LoginLogController constructor.
     * @param LoginLogRepository $logRepository
     */
    public function __construct(LoginLogRepository $logRepository)
    {
        $this->logRepository = $logRepository;
    }

    public function index()
    {
        $logs = $this->logRepository->findAllWithPagination();
        return view('admin.analytics.login-log', compact('logs'));
    }
}
