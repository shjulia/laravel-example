<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\Analytics;

use App\Entities\Notification\EmailLog;
use App\Http\Controllers\Controller;
use App\Repositories\Admin\Analytics\EmailsRepository;
use Illuminate\Support\Facades\Auth;

/**
 * Class EmailsController
 * @package App\Http\Controllers\Admin\Analytics
 */
class EmailsController extends Controller
{
    /**
     * @var EmailsRepository
     */
    private $emailsRepository;

    /**
     * EmailsController constructor.
     * @param EmailsRepository $emailsRepository
     */
    public function __construct(EmailsRepository $emailsRepository)
    {
        $this->emailsRepository = $emailsRepository;
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $emails = $this->emailsRepository->findMarketingEmailsAmount();
        return view('admin.analytics.emails.index', compact('emails'));
    }

    /**
     * @param int $key
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show(int $key)
    {
        if (!$subject = (EmailLog::MARKETING_SUBJECTS[$key - 1] ?? null)) {
            throw new \DomainException('Emails not found');
        }
        $emails = $this->emailsRepository->getMarketingEmailData((string)$subject);
        $admin = Auth::user();
        return view('admin.analytics.emails.show', compact('emails', 'subject', 'admin'));
    }
}
