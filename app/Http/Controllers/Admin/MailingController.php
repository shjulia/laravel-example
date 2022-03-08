<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\Emails\ReferralInfoJob;
use Illuminate\Http\Request;

/**
 * Class MailingController
 * @package App\Http\Controllers\Admin
 */
class MailingController extends Controller
{
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        return view('admin.mailing.index');
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    public function referralInfo(?bool $test = false)
    {
        try {
            ReferralInfoJob::dispatch($test ? false : true);
            return back()->with(['success' => 'You successfully run process.']);
        } catch (\Exception $e) {
            return back()->with(['error' => $e->getMessage()]);
        }
    }
}
