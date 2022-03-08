<?php

declare(strict_types=1);

namespace App\Console\Commands\Users\Practice;

use App\Entities\Notification\EmailMark;
use App\Entities\Shift\Coupon;
use App\Entities\User\User;
use App\Mail\Users\Practice\ContinueHiringMail;
use App\Repositories\User\PracticeRepository;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Mail;

/**
 * Class RemindContinueHiring
 * Reminds practice to continue hiring provider
 *
 * @package App\Console\Commands\Users\Practice
 */
class RemindContinueHiring extends Command
{
    /**
     * @var string
     */
    protected $signature = 'remind:continue-hiring';

    /**
     * @var string
     */
    protected $description = 'Remind practice to continue hiring provider';

    /**
     * @var PracticeRepository
     */
    private $practiceRepository;

    public const DAYS = [
        ['day' => 56, 'type' => EmailMark::AFTER_SHIFT_56_DAYS],
        ['day' => 35, 'type' => EmailMark::AFTER_SHIFT_35_DAYS],
        ['day' => 21, 'type' => EmailMark::AFTER_SHIFT_21_DAYS]
    ];

    /**
     * RemindContinueHiring constructor.
     * @param PracticeRepository $practiceRepository
     */
    public function __construct(PracticeRepository $practiceRepository)
    {
        parent::__construct();
        $this->practiceRepository = $practiceRepository;
    }

    public function handle()
    {
        $types = [];
        foreach (self::DAYS as $day) {
            $types[] = $day['type'];
            $practices = $this->practiceRepository->findPracticesWhereHasOldShifts($day['day'], $types);
            $this->handlePractices($practices, $day['type']);
        }
    }

    /**
     * @param Collection $practices
     * @param string $type
     */
    private function handlePractices(Collection $practices, string $type): void
    {
        foreach ($practices as $practice) {
            try {
                $coupon = Coupon::createBase(
                    $code = str_random(),
                    $start = new \DateTimeImmutable(),
                    $start->add(new \DateInterval('P7D')),
                    15.0,
                    null,
                    1,
                    1,
                    null,
                    $practice
                );
                $coupon->saveOrFail();
                $user = $practice->practiceCreator();
                Mail::to($practice->practiceCreator()->email)->send(new ContinueHiringMail($user, $code));
                $this->addEmailMark($user, $type);
            } catch (\Throwable $e) {
                \LogHelper::error($e);
                continue;
            }
        }
    }

    /**
     * @param User $user
     * @param string $type
     * @throws \Throwable
     */
    private function addEmailMark(User $user, string $type): void
    {
        $emailMark = EmailMark::createMark($user, $type);
        $emailMark->saveOrFail();
    }
}
