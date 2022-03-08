<?php

declare(strict_types=1);

namespace App\Console\Commands\Users\Provider;

use App\Entities\Notification\EmailMark;
use App\Entities\User\Provider\Specialist;
use App\Entities\User\User;
use App\Repositories\Notification\EmailMarkRepository;
use App\UseCases\Emails\Provider\ProfilePictureReminderService;
use Illuminate\Console\Command;

/**
 * Class UploadProfilePicture
 * Reminds to upload new profile picture.
 *
 * @package App\Console\Commands\Users\Provider
 */
class UploadProfilePicture extends Command
{
    /**
     * @var string
     */
    protected $signature = 'provider:upload-profile-picture';

    /**
     * @var string
     */
    protected $description = 'Remind to upload new profile picture';
    /**
     * @var ProfilePictureReminderService
     */
    private $pictureReminderService;
    /**
     * @var EmailMarkRepository
     */
    private $emailMarkRepository;

    /**
     * UploadProfilePicture constructor.
     * @param ProfilePictureReminderService $pictureReminderService
     * @param EmailMarkRepository $emailMarkRepository
     */
    public function __construct(
        ProfilePictureReminderService $pictureReminderService,
        EmailMarkRepository $emailMarkRepository
    ) {
        $this->pictureReminderService = $pictureReminderService;
        $this->emailMarkRepository = $emailMarkRepository;
        parent::__construct();
    }

    public function handle()
    {
        $providers = Specialist::whereHas('availabilities')->whereNull('photo')->with('user')->get();
        foreach ($providers as $provider) {
            /** @var User $user */
            $user = $provider->user;
            if ($this->emailMarkRepository->wasEmailSent($user, EmailMark::UPLOAD_PROFILE_PICTURE)) {
                continue;
            }
            $this->pictureReminderService->remindUploadPicture($user);
        }
    }
}
