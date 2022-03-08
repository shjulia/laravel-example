<?php

declare(strict_types=1);

namespace App\Console\Commands\Users\Practice;

use App\Entities\Notification\EmailMark;
use App\Entities\Shift\Coupon;
use App\Mail\Users\Practice\HireFirstProviderMail;
use App\Mail\Users\Practice\HireProviderMiddle;
use App\Repositories\User\PracticeRepository;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

/**
 * Class RemindHireMiddle
 * Remind practice to hire first provider after 45 days from registration.
 *
 * @package App\Console\Commands\Users\Practice
 */
class RemindHireMiddle extends Command
{
    /**
     * @var string
     */
    protected $signature = 'remind:hire-first-provider-middle';

    /**
     * @var string
     */
    protected $description = 'Remind practice to hire first provider after 45 days';

    /**
     * @var PracticeRepository
     */
    private $practiceRepository;

    /**
     * RemindHireMiddle constructor.
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
            45,
            EmailMark::HIRE_50_OFF
        );
        foreach ($practices as $practice) {
            try {
                $coupon = Coupon::createBase(
                    $code = str_random(),
                    $start = new \DateTimeImmutable(),
                    $start->add(new \DateInterval('P14D')),
                    50.0,
                    null,
                    1,
                    1,
                    null,
                    $practice
                );
                $coupon->saveOrFail();
                $user = $practice->practiceCreator();
                Mail::to($practice->practiceCreator()->email)->send(new HireProviderMiddle($user, $code));
                $emailMark = EmailMark::createMark($user, EmailMark::HIRE_50_OFF);
                $emailMark->saveOrFail();
            } catch (\Throwable $e) {
                \LogHelper::error($e);
                continue;
            }
        }
    }
}
