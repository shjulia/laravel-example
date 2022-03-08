<?php

declare(strict_types=1);

namespace App\UseCases\Emails\Provider;

use App\Entities\Notification\EmailMark;
use App\Entities\User\User;
use App\Mail\Users\Provider\UploadProfilePictureMail;
use Illuminate\Support\Facades\Mail;

/**
 * Class ProfilePictureReminderService
 * Reminds user that he needs to upload profile picture.
 *
 * @package App\UseCases\Emails\Provider
 */
class ProfilePictureReminderService
{
    /**
     * @param User $user
     */
    public function remindUploadPicture(User $user): void
    {
        try {
            Mail::to($user->email)->send(new UploadProfilePictureMail());
            $emailMark = EmailMark::createMark($user, EmailMark::UPLOAD_PROFILE_PICTURE);
            $emailMark->saveOrFail();
        } catch (\Throwable $e) {
            \LogHelper::error($e);
        }
    }
}
