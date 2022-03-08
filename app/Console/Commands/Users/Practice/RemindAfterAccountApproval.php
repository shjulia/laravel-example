<?php

declare(strict_types=1);

namespace App\Console\Commands\Users\Practice;

use App\Entities\Notification\EmailMark;
use App\Entities\User\Practice\Practice;
use App\Mail\Users\Practice\TempNotEligibleMail;
use App\Repositories\User\PracticeRepository;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

/**
 * Class RemindAfterAccountApproval
 * Reminds 12 days after account approval IF they have NOT yet requested a shift.
 *
 * @package App\Console\Commands\Users\Practice
 */
class RemindAfterAccountApproval extends Command
{
    /**
     * @var string
     */
    protected $signature = 'remind:after-account-approval';

    /**
     * @var string
     */
    protected $description = 'Remind 12 days after account approval IF they have NOT yet requested a shift';

    /**
     * @var PracticeRepository
     */
    private $practiceRepository;

    /**
     * RemindHireFirstProvider constructor.
     * @param PracticeRepository $practiceRepository
     */
    public function __construct(PracticeRepository $practiceRepository)
    {
        parent::__construct();
        $this->practiceRepository = $practiceRepository;
    }

    public function handle()
    {
        $practices = $this->practiceRepository->findWithNoShiftAfterDays(
            12,
            EmailMark::TEMP_NOT_ELIGIBLE
        );

        /** @var Practice $practice */
        foreach ($practices as $practice) {
            try {
                $user = $practice->practiceCreator();
                Mail::to($user->email)->send(new TempNotEligibleMail());
                $emailMark = EmailMark::createMark($user, EmailMark::TEMP_NOT_ELIGIBLE);
                $emailMark->saveOrFail();
            } catch (\Throwable $e) {
                \LogHelper::error($e);
                continue;
            }
        }
    }
}
