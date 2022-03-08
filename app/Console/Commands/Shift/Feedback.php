<?php

declare(strict_types=1);

namespace App\Console\Commands\Shift;

use App\Entities\Notification\EmailMark;
use App\Entities\Shift\Shift;
use App\Entities\User\User;
use App\Mail\Shift\YourFeedbackIsImportantMail;
use App\Repositories\Notification\EmailMarkRepository;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

/**
 * Class Feedback
 * Reminds practices and providers to leave review after shift was finished.
 *
 * @package App\Console\Commands\Shift
 */
class Feedback extends Command
{
    /**
     * @var string
     */
    protected $signature = 'shifts:feedback-check';

    /**
     * @var string
     */
    protected $description = 'Find shifts without feedback';

    private const WEEK = 168;
    /**
     * @var EmailMarkRepository
     */
    private $emailMarkRepository;

    /**
     * Feedback constructor.
     * @param EmailMarkRepository $emailMarkRepository
     */
    public function __construct(EmailMarkRepository $emailMarkRepository)
    {
        $this->emailMarkRepository = $emailMarkRepository;
        parent::__construct();
    }

    /**
     * @throws \Throwable
     */
    public function handle()
    {
        $today = date('Y-m-d');

        /** @var Shift[] $shifts */
        $shifts = Shift::where([
            'status' => Shift::STATUS_ACCEPTED_BY_PROVIDER,
            ['end_date', '<=', $today],
            'processed' => 1,
            'multi_days' => 0,
        ])
            ->whereDoesntHave('emailMarks', function ($q) {
                $q->where('type', EmailMark::FEEDBACK_IS_IMPORTANT_WEEK);
            })
            ->with(['practice', 'provider.user'])
            ->get();

        /** @var Shift $shift */
        foreach ($shifts as $shift) {
            $endsIn = $shift->endsInHours();
            if ($endsIn >= -12) {
                continue;
            }

            if ($endsIn <= -12 && $endsIn > -self::WEEK) {
                $this->sendEmail($shift, EmailMark::FEEDBACK_IS_IMPORTANT_12);
            } elseif ($endsIn <= -self::WEEK) {
                $this->sendEmail($shift, EmailMark::FEEDBACK_IS_IMPORTANT_WEEK);
            }
        }
    }

    /**
     * @param Shift $shift
     * @param string $type
     * @throws \Throwable
     */
    public function sendEmail(Shift $shift, string $type)
    {
        /** @var User $providerUser */
        $providerUser = $shift->provider->user;
        $date = Carbon::createFromTimeString($shift->date . ' ' . $shift->to_time, $providerUser->tz)->format('d F, Y');

        if (!$shift->isHasReviewFromProvider()) {
            /** @var User $practiceUser */
            $practiceUser = $shift->practice->practiceCreator();
            if ($this->emailMarkRepository->wasEmailSent($practiceUser, $type)) {
                return;
            }

            $providerName = $providerUser->full_name;
            $link = route('shifts.reviews.createReview', $shift);
            $photo = $shift->provider->photo_url;
            Mail::to($practiceUser->email)->send(new YourFeedbackIsImportantMail($providerName, $link, $date, $photo));
            $this->saveEmailMark($shift, $practiceUser, $type);
        }
        if (!$shift->isHasReviewFromPractice()) {
            if ($this->emailMarkRepository->wasEmailSent($providerUser, $type)) {
                return;
            }

            $practiceName = $shift->practice_location->practiceName;
            $link = route('shifts.provider.reviews.createReview', $shift);
            $photo = $shift->practice->practice_photo_url;
            Mail::to($providerUser->email)->send(new YourFeedbackIsImportantMail($practiceName, $link, $date, $photo));
            $this->saveEmailMark($shift, $providerUser, $type);
        }
    }

    /**
     * @param Shift $shift
     * @param User $user
     * @param string $type
     * @throws \Throwable
     */
    public function saveEmailMark(Shift $shift, User $user, string $type)
    {
        $emailMark = EmailMark::createMark($user, $type, $shift->id);
        $emailMark->saveOrFail();
    }
}
