<?php

declare(strict_types=1);

namespace App\Console\Commands\Users\Practice;

use App\Entities\Notification\EmailMark;
use App\Entities\Shift\Coupon;
use App\Mail\Users\Practice\HireFirstProviderMail;
use App\Repositories\User\PracticeRepository;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

/**
 * Class RemindHireFirstProvider
 * Reminds practice to hire first provider
 *
 * @package App\Console\Commands\Users
 */
class RemindHireFirstProvider extends Command
{
    /**
     * @var string
     */
    protected $signature = 'remind:hire-first-provider';

    /**
     * @var string
     */
    protected $description = 'Remind practice to hire first provider';

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
            7,
            EmailMark::HIRE_FIRST_PROVIDER
        );
        foreach ($practices as $practice) {
            try {
                $coupon = Coupon::createBase(
                    $code = str_random(),
                    $start = new \DateTimeImmutable(),
                    $start->add(new \DateInterval('P7D')),
                    10.0,
                    null,
                    1,
                    1,
                    null,
                    $practice
                );
                $coupon->saveOrFail();
                $user = $practice->practiceCreator();
                Mail::to($practice->practiceCreator()->email)->send(new HireFirstProviderMail($user, $code));
                $emailMark = EmailMark::createMark($user, EmailMark::HIRE_FIRST_PROVIDER);
                $emailMark->saveOrFail();
            } catch (\Throwable $e) {
                \LogHelper::error($e);
                continue;
            }
        }
    }
}
