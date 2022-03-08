<?php

declare(strict_types=1);

namespace App\UseCases\Auth\Partner;

use App\Entities\DTO\UserBase;
use App\Entities\User\User;
use App\Events\User\PartnerRegisterEvent;
use App\Events\User\SetPasswordEvent;
use App\Http\Requests\Auth\AdditionalRequest;
use App\Http\Requests\Auth\Partner\UserBaseRequest;
use App\Http\Requests\Auth\Partner\UserDetailsRequest;
use App\Notifications\User\AccountCreatedNotification;
use App\Notifications\User\SetPasswordNotification;
use App\UseCases\Auth\UserCreatorService;
use Illuminate\Contracts\Events\Dispatcher;

/**
 * Class RegisterService
 * Basic registration for user type Partner
 *
 * @package App\UseCases\Auth\Partner
 */
class RegisterService
{
    /**
     * @var UserCreatorService
     */
    private $userService;

    /**
     * @var Dispatcher
     */
    private $dispatcher;

    /**
     * RegisterService constructor.
     * @param UserCreatorService $userCreatorService
     * @param Dispatcher $dispatcher
     */
    public function __construct(UserCreatorService $userCreatorService, Dispatcher $dispatcher)
    {
        $this->userService = $userCreatorService;
        $this->dispatcher = $dispatcher;
    }

    /**
     * @param UserBaseRequest $request
     * @return User
     * @throws \Throwable
     */
    public function userBaseSave(UserBaseRequest $request): User
    {
        $data = new UserBase(
            $request->first_name,
            $request->last_name,
            $request->email,
            $request->phone,
            $request->industry,
            $request->password,
            $request->code
        );
        $data->viaLocation($request->lat, $request->lng);
        $user = $this->userService->createUser($data);
        return $user;
    }

    /**
     * @param AdditionalRequest $request
     * @param User $user
     */
    public function saveAdditionalUserData(AdditionalRequest $request, User $user): void
    {
        try {
            $this->userService->addNextData($user, $request->password, $request->phone);
            $user->update([
                'signup_step' => 'base:details'
            ]);
        } catch (\Exception $e) {
            \LogHelper::error($e, ['userId' => $user->id]);
            throw new \DomainException('Additional data saving error');
        }
    }

    /**
     * @param UserDetailsRequest $request
     * @param User $user
     * @return string
     * @throws \Throwable
     */
    public function detailsSave(UserDetailsRequest $request, User $user): string
    {
        try {
            if ($request->description == "provider" && $request->description_answer) {
                $this->userService->attachProvider($user);
                $user->update(['signup_step' => 'provider:industry']);
                $this->dispatcher->dispatch(new SetPasswordEvent($user));
                return 'provider:industry';
            }
            if ($request->description == "practice" && $request->description_answer) {
                $this->userService->attachPractice($user);
                $user->update(['signup_step' => 'practice:base']);
                $this->dispatcher->dispatch(new SetPasswordEvent($user));
                return 'practice:base';
            }

            $this->userService->attachPartner(
                $user,
                $request->description,
                $request->description_answer2 ?: $request->description_answer
            );
            $this->dispatcher->dispatch(new SetPasswordEvent($user));
            $this->dispatcher->dispatch(new PartnerRegisterEvent($user));

            $user->update([
                'tmp_token' => null,
                'signup_step' => null
            ]);
            return 'partner:success';
        } catch (\Exception $e) {
            \LogHelper::error($e, ['userId' => $user->id]);
            throw new \DomainException('Details creating error');
        }
    }
}
