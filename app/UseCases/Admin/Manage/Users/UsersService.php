<?php

declare(strict_types=1);

namespace App\UseCases\Admin\Manage\Users;

use App\Entities\DTO\SmsDTO;
use App\Entities\Invite\Invite;
use App\Entities\Notification\EmailLog;
use App\Entities\User\ApproveLog;
use App\Entities\User\Referral;
use App\Entities\User\User;
use App\Events\User\AccountRejected;
use App\Http\Requests\Admin\User\Edit\InviterRequest;
use App\Mail\Users\Provider\UpdatePhotoMail;
use App\Notifications\SmsNotification;
use App\Repositories\User\SpecialistRepository;
use App\Repositories\User\UserRepository;
use App\Services\ImageAnalysis\CompareFaces;
use App\Services\Mail\MailgunService;
use App\UseCases\Emails\Provider\ProfilePictureReminderService;
use App\UseCases\Shift\PaymentService;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Support\Facades\Mail;
use App\Events\User\Provider\AccountApproved as AccountApprovedProvider;
use App\Events\User\Practice\AccountApproved as AccountApprovedPractice;

/**
 * Class UsersService
 * Provides actions with users: approve, reject, compare, delete, check, request photo, show email logs, delete,
 *
 * @package App\UseCases\Admin\Manage\Users
 */
class UsersService
{
    /**
     * @var CompareFaces
     */
    private $compareFaces;

    /**
     * @var SpecialistRepository
     */
    private $specialistRepository;

    /**
     * @var UserRepository
     */
    private $userRepository;
    /**
     * @var Dispatcher
     */
    private $dispatcher;

    /**
     * @var MailgunService
     */
    private $mailgunService;
    /**
     * @var ProfilePictureReminderService
     */
    private $profilePictureReminderService;

    /**
     * UsersService constructor.
     * @param CompareFaces $compareFaces
     * @param SpecialistRepository $specialistRepository
     * @param UserRepository $userRepository
     * @param Dispatcher $dispatcher
     * @param MailgunService $mailgunService
     * @param ProfilePictureReminderService $profilePictureReminderService
     */
    public function __construct(
        CompareFaces $compareFaces,
        SpecialistRepository $specialistRepository,
        UserRepository $userRepository,
        Dispatcher $dispatcher,
        MailgunService $mailgunService,
        ProfilePictureReminderService $profilePictureReminderService
    ) {
        $this->compareFaces = $compareFaces;
        $this->specialistRepository = $specialistRepository;
        $this->userRepository = $userRepository;
        $this->dispatcher = $dispatcher;
        $this->mailgunService = $mailgunService;
        $this->profilePictureReminderService = $profilePictureReminderService;
    }

    /**
     * @param User $user
     * @param User $admin
     * @param string|null $reason
     */
    public function approveProvider(User $user, User $admin, ?string $reason = null): void
    {
        if (!$provider = $user->specialist) {
            throw new \DomainException('User role error');
        }
        $provider->changeApprovalStatus($reason);
        try {
            $provider->saveOrFail();
        } catch (\Throwable $e) {
            throw new \DomainException('Saving status error');
        }
        if ($provider->isApproved()) {
            $user->approveLogs()->create([
                'admin_id' => $admin->id,
                'status' => ApproveLog::CHANGED_TO_APPROVED
            ]);
            $this->dispatcher->dispatch(new AccountApprovedProvider($user));
        } else {
            $user->approveLogs()->create([
                'admin_id' => $admin->id,
                'status' => ApproveLog::CHANGED_TO_WAITING
            ]);
        }
    }

    /**
     * @param User $user
     * @param User $admin
     */
    public function approvePractice(User $user, User $admin): void
    {
        if (!$practice = $user->practice) {
            throw new \DomainException('User role error');
        }
        $practice->changeApprovalStatus();
        try {
            $practice->saveOrFail();
        } catch (\Throwable $e) {
            throw new \DomainException('Saving status error');
        }
        if ($practice->isApproved()) {
            $user->approveLogs()->create([
                'admin_id' => $admin->id,
                'status' => ApproveLog::CHANGED_TO_APPROVED
            ]);
            $this->dispatcher->dispatch(new AccountApprovedPractice($user));
        } else {
            $user->approveLogs()->create([
                'admin_id' => $admin->id,
                'status' => ApproveLog::CHANGED_TO_WAITING
            ]);
        }
    }

    /*public function approve(User $user, User $admin, bool $isApproved): void
    {
        if ($isApproved) {
            $user->setActiveStatus();
            $user->approveLogs()->create([
                'admin_id' => $admin->id,
                'status' => ApproveLog::CHANGED_TO_APPROVED
            ]);
            $this->dispatcher->dispatch(new AccountApproved($user));
        } else {
            $user->setWaitingStatus();
            $user->approveLogs()->create([
                'admin_id' => $admin->id,
                'status' => ApproveLog::CHANGED_TO_WAITING
            ]);
        }
        try {
            $user->saveOrFail();
        } catch (\Throwable $e) {
            throw new \DomainException('Saving status error');
        }
    }*/

    /**
     * @param User $user
     * @param User|null $admin
     */
    public function reject(User $user, ?User $admin = null): void
    {
        try {
            $user->reject();
            $user->save();
            $user->approveLogs()->create([
                'admin_id' => $admin ? $admin->id : null,
                'status' => 'rejected',
                'desc' => $admin ? '' : 'Changed by background check'
            ]);
            $this->dispatcher->dispatch(new AccountRejected($user));
        } catch (\Exception $e) {
            throw new \DomainException('User rejecting error');
        }
    }

    /**
     * @param User $user
     * @param User $admin
     */
    public function unreject(User $user, User $admin): void
    {
        try {
            $user->unReject();
            $user->save();
            $user->approveLogs()->create([
                'admin_id' => $admin->id,
                'status' => 'un-rejected',
            ]);
        } catch (\Exception $e) {
            throw new \DomainException('User un-rejecting error');
        }
    }

    /**
     * @param User $user
     */
    public function deleteUser(User $user): void
    {
        try {
            $user->delete();
        } catch (\Exception $e) {
            throw new \DomainException('User deleting error');
        }
    }

    /**
     * @param User $user
     */
    public function compare(User $user): void
    {
        $path1 = $user->specialist->driver_photo;
        $path2 = $user->specialist->photo;
        if (!$path1 || !$path2) {
            throw new \DomainException('Two photos must be set');
        }
        try {
            $result = $this->compareFaces->analyzeImage($path1, $path2);
            $user->specialist->update([
               'photos_similar' => $result
            ]);
            if ($result < 85) {
                $this->profilePictureReminderService->remindUploadPicture($user);
            }
        } catch (\Exception $e) {
            throw new \DomainException('Compare faces error');
        }
    }

    /**
     * @param User $user
     */
    public function setUserToTest(User $user): void
    {
        try {
            $user->setTestAccount();
            $user->save();
        } catch (\Exception $e) {
            throw new \DomainException('Error');
        }
    }

    /**
     * @param int $id
     */
    public function resendMessage(int $id): void
    {
        /** @var EmailLog $emailLog */
        $emailLog = EmailLog::where('id', $id)->first();
        if ($emailLog->isEmail()) {
            $this->mailgunService->resendMessage($id);
        } elseif ($emailLog->isSms()) {
            $user = $emailLog->user;
            $user->notify(new SmsNotification(new SmsDTO($emailLog->data)));
        }
    }

    /**
     * @param User $user
     * @return EmailLog[]
     */
    public function getEmailLogs(User $user)
    {
        return $this->userRepository->findEmailLogs($user);
    }

    /**
     * @param User $user
     * @return bool
     */
    public function checkReferredIsWorked(User $user): bool
    {
        $worked = false;
        if ($user->isProvider()) {
            $worked = (bool)$user->specialist->jobs_total;
        }
        if ($worked) {
            return $worked;
        }
        if ($user->isPractice()) {
            $worked = (bool)$user->practice->hires_total;
        }
        return $worked;
    }

    /**
     * @param User $user
     * @param InviterRequest $request
     */
    public function setInviter(User $user, InviterRequest $request): void
    {
        $invite = Invite::updateOrCreate([
            'referral_id' => $request->user_id,
            'email' => $user->email,
        ], [
            'referral_id' => $request->user_id,
            'email' => $user->email,
            'user_id' => $user->id,
            'accepted' => 1
        ]);
        $referral = $invite->referral;
        $referral->update([
            'referred_amount' => $referral->referral_amount + 1
        ]);

        if ((bool)$request->pay) {
            /** @var PaymentService $paymentService */
            $paymentService = app()->get(PaymentService::class);
            $amount = (float)Referral::REFERRAL_FEE;
            $paymentService->replenish($referral->user, $amount, 'For Invitation of user #' . $invite->user_id);
            $paymentService->withdraw($referral->user, $amount, false, false);
        }
    }
}
