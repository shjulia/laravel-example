<?php

declare(strict_types=1);

namespace App\UseCases\Auth\Practice;

use App\Entities\DTO\Notification;
use App\Entities\DTO\UserBase;
use App\Entities\User\User;
use App\Events\Maps\Geocoder\PracticeGeocodeEvent;
use App\Events\Maps\PlacePhotoEvent;
use App\Events\User\ActionLogEvent;
use App\Events\User\SetPasswordEvent;
use App\Events\User\SuccessfulRegistrationEvent;
use App\Helpers\S3Helper;
use App\Http\Requests\Auth\AdditionalRequest;
use App\Http\Requests\Auth\Practice\BaseInfoRequest;
use App\Http\Requests\Auth\Practice\IndustryRequest;
use App\Http\Requests\Auth\Practice\InsuranceRequest;
use App\Http\Requests\Auth\Practice\UploadImageOrPDFRequest;
use App\Http\Requests\Auth\Practice\UserBaseRequest;
use App\Http\Requests\Auth\Provider\UploadImageRequest;
use App\Notifications\GlobalNotification;
use App\Notifications\User\AccountCreatedNotification;
use App\Notifications\User\SetPasswordNotification;
use App\Repositories\User\PracticeRepository;
use App\Services\Maps\AutocompletePlaceService;
use App\Services\Maps\GeocodeService;
use App\UseCases\Auth\UserCreatorService;
use Carbon\Carbon;
use Illuminate\Contracts\Events\Dispatcher;

/**
 * Class RegisterService
 * Basic registration for user type Partner Practice
 *
 * @package App\UseCases\Auth\Practice
 */
class RegisterService
{
    /**
     * @var UserCreatorService
     */
    private $userService;

    /**
     * @var AutocompletePlaceService
     */
    private $placeService;

    /** @var PracticeRepository $practiceRepository */
    private $practiceRepository;

    /**
     * @var Dispatcher
     */
    private $dispatcher;

    /**
     * @var User|null
     */
    private $admin = null;

    /**
     * RegisterService constructor.
     * @param UserCreatorService $userCreatorService
     * @param AutocompletePlaceService $placeService
     * @param PracticeRepository $practiceRepository
     * @param Dispatcher $dispatcher
     */
    public function __construct(
        UserCreatorService $userCreatorService,
        AutocompletePlaceService $placeService,
        PracticeRepository $practiceRepository,
        Dispatcher $dispatcher
    ) {
        $this->userService = $userCreatorService;
        $this->placeService = $placeService;
        $this->practiceRepository = $practiceRepository;
        $this->dispatcher = $dispatcher;
    }

    /**
     * @param User $admin
     */
    public function setAdmin(User $admin): void
    {
        $this->admin = $admin;
    }

    /**
     * @param UserBaseRequest $request
     * @return User
     * @throws \Exception
     */
    public function saveUserBase(UserBaseRequest $request): User
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
        $data->toPractice();
        $data->viaLocation($request->lat, $request->lng);
        $user = $this->userService->createUser($data);
        $this->dispatcher->dispatch(new SetPasswordEvent($user));
        $this->dispatcher->dispatch(new ActionLogEvent($user, 'Base practice creation', $this->admin));
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
                'signup_step' => $user->practice->industry_id ? 'practice:base' : 'practice:industry'
            ]);
            $this->dispatcher->dispatch(new ActionLogEvent($user, 'Password set up', $this->admin));
        } catch (\Exception $e) {
            \LogHelper::error($e, ['userId' => $user->id]);
            throw new \DomainException('Additional data saving error');
        }
    }

    /**
     * @param IndustryRequest $request
     * @param User $user
     */
    public function saveIndustry(IndustryRequest $request, User $user): void
    {
        try {
            $user->practice->update([
                'industry_id' => $request->industry
            ]);
            $user->update([
                'signup_step' => 'practice:base'
            ]);
            $this->dispatcher->dispatch(
                new ActionLogEvent($user, 'Industry selection (' . $request->industry . ')', $this->admin)
            );
        } catch (\Exception $e) {
            \LogHelper::error($e, ['userId' => $user->id]);
            throw new \DomainException('Industry saving error');
        }
    }

    /**
     * @param string $query
     * @param null|string $lat
     * @param null|string $lng
     * @return array
     */
    public function autocompleteQuery(string $query, ?string $lat, ?string $lng): array
    {
        return $this->placeService->getPlacesNamesByQuery($query, $lat, $lng, true);
    }

    /**
     * @param string $query
     * @return array
     */
    public function getPlaceData(string $query): array
    {
        return $this->placeService->getPlaceData($query);
    }

    /**
     * @param BaseInfoRequest $request
     * @param User $user
     * @param bool|null $isStep
     */
    public function saveBaseInfo(BaseInfoRequest $request, User $user, ?bool $isStep = true): void
    {
        try {
            $practice = $user->practice;
            $practice->setBaseInfo(
                $request->name,
                $request->address,
                $request->city,
                $request->state,
                $request->zip,
                $request->url,
                $request->phone
            );
            $practice->saveOrFail();
            if (!$this->admin) {
                $user->update([
                    'signup_step' => 'practice:insurance'
                ]);
            }

            $this->practiceRepository->setArea($user->practice, $request->state, $request->city, $request->zip);

            $this->dispatcher->dispatch(new PlacePhotoEvent($user->practice));
            $this->dispatcher->dispatch(new PracticeGeocodeEvent($user->practice));
            $this->dispatcher->dispatch(new ActionLogEvent($user, 'Practice base info set', $this->admin));
        } catch (\Throwable $e) {
            \LogHelper::error($e, ['userId' => $user->id]);
            throw new \DomainException('Practice base info saving error');
        }
    }

    /**
     * @param UploadImageRequest $request
     * @param User $user
     * @return string
     */
    public function uploadPolicyPhoto(UploadImageOrPDFRequest $request, User $user): string
    {
        $path = S3Helper::uploadImage($request->photo);
        $user->practice->update([
            'policy_photo' => $path
        ]);
        $this->dispatcher->dispatch(new ActionLogEvent($user, 'Policy photo uploaded (' . $path . ')', $this->admin));
        return $user->practice->policy_photo_url;
    }

    /**
     * @param User $user
     */
    public function removePolicyPhoto(User $user): void
    {
        $user->practice->update([
            'policy_photo' => null
        ]);
        $this->dispatcher->dispatch(new ActionLogEvent($user, 'Policy photo removed', $this->admin));
    }

    /**
     * @param InsuranceRequest $request
     * @param User $user
     * @param bool|null $isStep
     */
    public function saveInsurance(InsuranceRequest $request, User $user, ?bool $isStep = true): void
    {
        try {
            $user->practice->update([
                'policy_type' => $request->type,
                'policy_number' => $request->number,
                'policy_expiration_date' => $request->expiration_date ? Carbon::createFromTimestamp(
                    strtotime($request->expiration_date)
                ) : null,
                'policy_provider' => $request->provider,
                'no_policy' => $request->no_policy
            ]);
            if (!$this->admin) {
                $user->update([
                    'tmp_token' => null,
                    'signup_step' => null
                ]);

                $this->dispatcher->dispatch(new SuccessfulRegistrationEvent($user, route('practice.details.base')));
            }
            $this->dispatcher->dispatch(new ActionLogEvent($user, 'Insurance saved', $this->admin));
        } catch (\Exception $e) {
            \LogHelper::error($e, ['userId' => $user->id]);
            throw new \DomainException('Practice base info saving error');
        }
    }
}
