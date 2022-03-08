<?php

declare(strict_types=1);

namespace App\UseCases\Auth;

use App\Entities\DTO\UserBase;
use App\Entities\Industry\Industry;
use App\Entities\Invite\Invite;
use App\Entities\User\Practice\Practice;
use App\Entities\User\Provider\Specialist;
use App\Entities\User\Referral;
use App\Entities\User\Role;
use App\Entities\User\SignupAutosave;
use App\Entities\User\User;
use App\Events\User\ActionLogEvent;
use App\Helpers\ReferralCodeGenerator;
use App\Mail\Shift\ReferredFriend;
use App\Repositories\Industry\IndustryRepository;
use App\Repositories\User\ReferralRepository;
use App\Repositories\User\RolesRepository;
use App\Repositories\User\UserRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

/**
 * Class UserCreatorService
 * Provides basic user creation functionality.
 *
 * @package App\UseCases\Auth
 */
class UserCreatorService
{
    /**
     * @var IndustryRepository
     */
    private $industryRepository;

    /**
     * @var RolesRepository
     */
    private $rolesRepository;

    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @var ReferralRepository
     */
    private $referralRepository;

    /**
     * UserCreatorService constructor.
     * @param IndustryRepository $industryRepository
     * @param RolesRepository $rolesRepository
     * @param UserRepository $userRepository
     * @param ReferralRepository $referralRepository
     */
    public function __construct(
        IndustryRepository $industryRepository,
        RolesRepository $rolesRepository,
        UserRepository $userRepository,
        ReferralRepository $referralRepository
    ) {
        $this->industryRepository = $industryRepository;
        $this->rolesRepository = $rolesRepository;
        $this->userRepository = $userRepository;
        $this->referralRepository = $referralRepository;
    }

    /**
     * @param UserBase $data
     * @return User
     * @throws \Exception
     */
    public function createUser(UserBase $data): User
    {
        DB::beginTransaction();
        try {
            $industry = $this->getIndustry($data->industry);

            $user = $this->userRepository->findByEmailAndRole($data->email, $data->role);
            $step = $data->isPractice()
                ? 'practice:base'
                : ($data->isProvider() ? 'provider:industry' : 'base:details');
            if ($user) {
                $user->editByNewType($data->first_name, $data->last_name, $step, $data->phone);
            } else {
                $user = User::createBySignUp($data->email, $data->first_name, $data->last_name, $step);
            }
            $user->saveOrFail();
            $user->setAppends(['tmp_password']);
            $this->addLocationInfo($user, $data);

            if ($data->code) {
                $this->acceptInvite($data->code, $user);
            }

            if ($data->isPractice()) {
                $this->attachPractice($user, $industry);
            } elseif ($data->isProvider()) {
                $this->attachProvider($user, $industry);
            }
            $this->createReferral($user);
            SignupAutosave::where('email', $user->email)->delete();

            DB::commit();
        } catch (\Throwable $e) {
            \LogHelper::error($e, ['userId' => $user->id]);
            DB::rollback();
            throw new \DomainException('User saving error');
        }

        return $user;
    }

    /**
     * @param int|null $industry
     * @return Industry
     */
    private function getIndustry(?int $industry = null): Industry
    {
        $industry = $this->industryRepository->getByIndustry($industry);
        if (!$industry) { // remove this rows after allowing All industries
            $industry = $this->industryRepository->getDentalIndustry();
        }
        return $industry;
    }

    /**
     * @param User $user
     * @param string $password
     * @param null|string $phone
     */
    public function addNextData(User $user, string $password, ?string $phone): void
    {
        $user->update([
            'phone' => $phone,
            'password' => bcrypt($password),
        ]);
    }

    /**
     * @param string $code
     * @param User $user
     */
    private function acceptInvite(string $code, User $user): void
    {
        $referral = $this->referralRepository->findByCode($code);
        if (!$referral) {
            return;
        }
        $referral->update([
            'referred_amount' => $referral->referred_amount + 1
        ]);
        Invite::updateOrCreate([
            'email' => $user->email,
            'referral_id' => $referral->user_id
        ], [
            'user_id' => $user->id,
            'accepted' => Invite::ACCEPTED
        ]);
        Mail::to($referral->user)->send(new ReferredFriend($user->full_name));
    }

    /**
     * @param User $user
     */
    private function createReferral(User $user): void
    {
        if ($user->referral) {
            return;
        }

        $user->referral()->create([
            'referral_code' => ReferralCodeGenerator::generate(),
            'referred_amount' => 0,
            'referral_money_earned' => 0
        ]);
    }

    /**
     * @param User $user
     * @param Industry|null $industry
     * @throws \Throwable
     */
    public function attachPractice(User $user, ?Industry $industry = null): void
    {
        $practice = Practice::createBase($industry ?: $this->getIndustry());
        $practice->saveOrFail();

        $user->practices()->attach($practice->id, [
            'is_creator' => 1,
            'practice_role' => Role::PRACTICE_ADMINISTRATOR
        ]);

        $role = $this->rolesRepository->getPracticeRole();
        $user->roles()->attach($role->id);
    }

    /**
     * @param User $user
     * @param Industry|null $industry
     * @throws \Throwable
     */
    public function attachProvider(User $user, ?Industry $industry = null): void
    {
        $provider = Specialist::createBase($user, $industry ?: $this->getIndustry());
        $provider->saveOrFail();
        $role = $this->rolesRepository->getProviderRole();
        $user->roles()->attach($role->id);
        $user->specialist->checkr()->create();
    }

    /**
     * @param User $user
     * @param string $description
     * @param null|string $descriptionAnswer
     */
    public function attachPartner(User $user, string $description, ?string $descriptionAnswer = null): void
    {
        $user->partner()->create([
            'description' => $description,
            'description_answer' => $descriptionAnswer ?: 'no'
        ]);

        $role = $this->rolesRepository->getPartnerRole();
        $user->roles()->attach($role->id);
    }

    /**
     * @param User $user
     * @param UserBase $data
     */
    private function addLocationInfo(User $user, UserBase $data): void
    {
        if (!$data->lat || !$data->lng) {
            event(new ActionLogEvent($user, 'Signup without location detect accepting'));
            return;
        }
        event(new ActionLogEvent(
            $user,
            'Signup in the location (' . $data->lat . ', ' . $data->lng . ')'
        ));
    }
}
