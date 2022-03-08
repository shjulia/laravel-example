<?php

declare(strict_types=1);

namespace App\Console\Commands\Shift;

use App\Entities\User\Practice\Practice;
use App\Entities\User\Provider\Specialist;
use App\Mail\Shift\MonthlySummary\MonthlySummaryPracticeMail;
use App\Mail\Shift\MonthlySummary\MonthlySummaryProviderMail;
use App\UseCases\Shift\ShiftSummaryService;
use Illuminate\Console\Command;
use Illuminate\Contracts\Mail\Mailer;

/**
 * Class MonthlySummaryReport
 * Sends monthly summary email to practices and providers.
 *
 * @package App\Console\Commands\Shift
 */
class MonthlySummaryReport extends Command
{
    /**
     * @var string
     */
    protected $signature = 'shifts:report';

    /**
     * @var string
     */
    protected $description = 'Send monthly summary emails';

    /**
     * @var ShiftSummaryService
     */
    private $summaryService;

    /**
     * @var Mailer
     */
    private $mailer;

    public function __construct(ShiftSummaryService $summaryService, Mailer $mailer)
    {
        $this->summaryService = $summaryService;
        $this->mailer = $mailer;
        parent::__construct();
    }

    public function handle()
    {
        $providerShifts = $this->summaryService->getShiftsGroupedByProvider();
        foreach ($providerShifts as $providerId => $shiftGroup) {
            $provider = Specialist::find($providerId);
            $this->mailer->to($provider)->send(new MonthlySummaryProviderMail($provider, $shiftGroup));
        }

        $practiceShifts = $this->summaryService->getShiftsGroupedByPractice();
        foreach ($practiceShifts as $practiceId => $shiftGroup) {
            $practice = Practice::find($practiceId);
            $this->mailer->to($practice)->send(new MonthlySummaryPracticeMail($practice, $shiftGroup));
        }
    }
}
