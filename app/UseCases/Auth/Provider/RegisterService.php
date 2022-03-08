<?php

declare(strict_types=1);

namespace App\UseCases\Auth\Provider;

use App\Entities\DTO\UserBase;
use App\Entities\User\Provider\Specialist;
use App\Events\Maps\Geocoder\SpecialistGeocodeEvent;
use App\Events\User\ActionLogEvent;
use App\Events\User\Provider\DLUpdated;
use App\Events\User\SetPasswordEvent;
use App\Events\User\SuccessfulRegistrationEvent;
use App\Exceptions\User\SSNIsAlreadyRegisteredException;
use App\Helpers\EncryptHelper;
use App\Helpers\S3Helper;
use App\Http\Requests\Auth\AdditionalRequest;
use App\Http\Requests\Auth\Provider\IdentityEditRequest;
use App\Http\Requests\Auth\Provider\OneLicenseRequest;
use App\Http\Requests\Auth\Provider\UserBaseRequest;
use App\Entities\User\User;
use App\Http\Requests\Auth\Provider\IndustryRequest;
use App\Http\Requests\Auth\Provider\IdentityRequest;
use App\Http\Requests\Auth\Provider\LicenceRequest;
use App\Http\Requests\Auth\Provider\CheckRequest;
use App\Http\Requests\Auth\Provider\UploadImageRequest;
use App\Services\Auth\Provider\Driver\CreateService;
use App\Services\Auth\Provider\Driver\DriverLicense\Photo\AddService;
use App\Services\Auth\Provider\Driver\SSNService;
use App\Services\Wallet\Provider\WalletService;
use Illuminate\Contracts\Events\Dispatcher;
use App\Repositories\User\SpecialistRepository;
use App\UseCases\Auth\UserCreatorService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Class RegisterService
 * Basic registration for user type Provider
 *
 * @package App\UseCases\Auth\Provider
 */
class RegisterService
{
    /**
     * @var UserCreatorService
     */
    private $userService;

    /**
     * @var SpecialistRepository
     */
    private $specialistRepository;

    /**
     * @var Dispatcher
     */
    private $dispatcher;

    /**
     * @var User|null
     */
    private $admin = null;
    /**
     * @var CreateService
     */
    private $coreCreateService;
    /**
     * @var AddService
     */
    private $coreAddService;
    /**
     * @var SSNService
     */
    private $ssnService;
    /**
     * @var WalletService
     */
    private $walletService;

    public function __construct(
        UserCreatorService $userCreatorService,
        SpecialistRepository $specialistRepository,
        Dispatcher $dispatcher,
        CreateService $coreCreateService,
        AddService $coreAddService,
        SSNService $ssnService,
        WalletService $walletService
    ) {
        $this->userService = $userCreatorService;
        $this->specialistRepository = $specialistRepository;
        $this->dispatcher = $dispatcher;
        $this->coreCreateService = $coreCreateService;
        $this->coreAddService = $coreAddService;
        $this->ssnService = $ssnService;
        $this->walletService = $walletService;
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
        $data->toProvider();
        $data->viaLocation($request->lat, $request->lng);
        $user = $this->userService->createUser($data);
        $data->uuid = $user->uuid;
        try {
            $this->coreCreateService->createDriver($data);
        } catch (\DomainException $e) {
            //not very important so logged at the service
        }
        $this->dispatcher->dispatch(new SetPasswordEvent($user));
        $this->dispatcher->dispatch(new ActionLogEvent($user, 'Base provider creation'));
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
                'signup_step' => 'provider:industry'
            ]);
            $this->dispatcher->dispatch(new ActionLogEvent($user, 'Password set up'));
        } catch (\Exception $e) {
            \LogHelper::error($e, ['userId' => $user->id]);
            throw new \DomainException('Additional data saving error');
        }
    }

    /**
     * @param IndustryRequest $request
     * @param User $user
     */
    public function industrySave(IndustryRequest $request, User $user): void
    {
        try {
            $user->specialist->update([
                'industry_id' => $request->industry,
                'position_id' => $request->position
            ]);
            $user->update([
               'signup_step' => 'provider:identity'
            ]);
            $this->dispatcher->dispatch(new ActionLogEvent($user, 'Position selection (' . $request->position . ')'));
        } catch (\Exception $e) {
            \LogHelper::error($e, ['userId' => $user->id]);
            throw new \DomainException('Industry saving error');
        }
    }

    /**
     * @param string|null $phone
     * @param User $user
     */
    public function setPhone(?string $phone, User $user): void
    {
        if (!$phone) {
            throw new \DomainException('Phone must be set');
        }
        try {
            $user->update([
                'phone' => $phone
            ]);
            $this->dispatcher->dispatch(new ActionLogEvent($user, 'Phone set (' . $phone . ')', $this->admin));
        } catch (\Exception $e) {
            \LogHelper::error($e, ['userId' => $user->id]);
            throw new \DomainException('Phone saving error');
        }
    }

    /**
     * @param IdentityRequest $request
     * @param User $user
     * @param bool $isStep
     */
    public function identitySave(IdentityRequest $request, User $user, ?bool $isStep = true): void
    {
        try {
            $specialist = $user->specialist;
            $specialist->update([
                'driver_license_number' => $request->license,
                'driver_address' => $request->address,
                'driver_city' => $request->city,
                'driver_state' => $request->state,
                'driver_zip' => $request->zip,
                'dob' => Carbon::parse($request->dob)->format('Y-m-d'),
                'driver_expiration_date' => Carbon::parse($request->expiration_date)->format('Y-m-d'),
                'driver_gender' => $request->gender,
                'driver_first_name' => $request->first_name ?: $user->first_name,
                'driver_last_name' => $request->last_name ?: $user->last_name,
                'driver_middle_name' => $request->middle_name,
            ]);

            if ($isStep) {
                $user->update([
                    'signup_step' => 'provider:license'
                ]);
            }

            $this->specialistRepository->setArea($specialist, $request->state, $request->city, $request->zip);
            $this->dispatcher->dispatch(new DLUpdated($specialist));
            $this->dispatcher->dispatch(new SpecialistGeocodeEvent($user->specialist));
            $this->dispatcher->dispatch(new ActionLogEvent($user, 'Identity set', $this->admin));
        } catch (\Exception $e) {
            \LogHelper::error($e, ['userId' => $user->id]);
            throw new \DomainException('Identity saving error');
        }
    }

    /**
     * @param User $user
     */
    public function identityRemove(User $user): void
    {
        try {
            $specialist = $user->specialist;
            $specialist->update([
                'driver_photo' => null,
                'driver_license_number' => null,
                'driver_address' => null,
                'driver_city' => null,
                'driver_state' => null,
                'driver_zip' => null,
                'dob' => null,
                'driver_expiration_date' => null,
                'driver_gender' => null,
            ]);
            $this->dispatcher->dispatch(new ActionLogEvent($user, 'Identity removed', $this->admin));
        } catch (\Exception $e) {
            \LogHelper::error($e, ['userId' => $user->id]);
            throw new \DomainException('Identity removing error');
        }
    }

    /**
     * @param UploadImageRequest $request
     * @param User $user
     * @return array
     */
    public function uploadDriverLicense(UploadImageRequest $request, User $user): string
    {
        $path = S3Helper::uploadImage($request->photo);
        $user->specialist->update([
            'driver_photo' => $path
        ]);
        $this->dispatcher->dispatch(new ActionLogEvent($user, 'Driver license uploaded (' . $path . ')', $this->admin));
        return $path;
    }

    public function analyzeImage(string $path, User $user): array
    {
        $data = $this->coreAddService->add($user->uuid, S3Helper::getUrlByPath($path));
        $user->specialist()->update([
            'driver_license_number' => $data['number'],
            'driver_address' => $data['address']['address'],
            'driver_city' => $data['address']['city'],
            'driver_state' => $data['address']['state'],
            'driver_zip' => $data['address']['zip'] ,
            'driver_first_name' => $data['name']['first'],
            'driver_last_name' => $data['name']['last'],
            'driver_middle_name' => $data['name']['middle'],
            'dob' => $data['birthDate'] ? Carbon::parse($data['birthDate'])->format('Y-m-d') : null,
            'driver_expiration_date' => $data['expirationDate']
                ? Carbon::parse($data['expirationDate'])->format('Y-m-d')
                : null,
            'driver_gender' => $data['gender']
        ]);
        return array_merge($data, ['photo_url' => $user->specialist->driver_photo_url]);
    }

    /**
     * @param UploadImageRequest $request
     * @param User $user
     * @return string
     */
    public function uploadMedicalLicense(UploadImageRequest $request, User $user): string
    {
        $path = S3Helper::uploadImage($request->photo);
        $user->specialist->licenses()->updateOrCreate(
            ['position' => $request->position],
            ['photo' => $path, 'is_main' => 0]
        );
        $this->dispatcher->dispatch(
            new ActionLogEvent($user, 'Medical license uploaded (' . $path . ')', $this->admin)
        );
        return S3Helper::getUrlByPath($path);
    }

    /**
     * @param LicenceRequest $request
     * @param User $user
     * @param User|null $admin
     */
    public function licenseSave(LicenceRequest $request, User $user): void
    {
        try {
            /** @var Specialist $specialist */
            $specialist = $user->specialist;
            foreach ($request->position as $i => $position) {
                if (!$request->number[$i]) {
                    continue;
                }
                $specialist->licenses()->updateOrCreate(
                    ['position' => $position],
                    [
                        'type' => $request->type[$i],
                        'state' => $request->state[$i],
                        'number' => $request->number[$i],
                        'expiration_date' => Carbon::parse($request->expiration_date[$i])->format('Y-m-d'),
                        'position' => $position
                    ]
                );
            }

            $this->dispatcher->dispatch(new ActionLogEvent($user, 'Medical licenses creation', $this->admin));
            if (!$user->tmp_token) {
                return;
            }

            $user->update([
                'signup_step' => 'provider:check'
            ]);
        } catch (\Exception $e) {
            \LogHelper::error($e, ['userId' => $user->id]);
            throw new \DomainException('Medical license saving error');
        }
    }

    /**
     * @param OneLicenseRequest $request
     * @param User $user
     */
    public function oneLicenseSave(OneLicenseRequest $request, User $user): void
    {
        try {
            /** @var Specialist $specialist */
            $specialist = $user->specialist;
            $specialist->licenses()->updateOrCreate(
                ['position' => $request->position],
                [
                    'type' => $request->type,
                    'state' => $request->state,
                    'number' => $request->number,
                    'expiration_date' => $request->expiration_date
                        ? Carbon::parse($request->expiration_date)->format('Y-m-d')
                        : null
                ]
            );
            $this->dispatcher->dispatch(new ActionLogEvent($user, 'One medical license saved', $this->admin));
        } catch (\Exception $e) {
            \LogHelper::error($e, ['userId' => $user->id]);
            throw new \DomainException('Medical license saving error');
        }
    }

    /**
     * @param int $position
     * @param User $user
     * @throws \Exception
     */
    public function licenseRemove(int $position, User $user)
    {
        /** @var Specialist $specialist */
        $specialist = $user->specialist;
        DB::beginTransaction();
        try {
            if (!$license = $specialist->licenses()->where('position', $position)->first()) {
                return;
            }
            $license->delete();
            foreach ($specialist->licenses as $license) {
                if ($license->position > $position) {
                    $license->update(['position' => $license->position - 1]);
                }
            }
            $this->dispatcher->dispatch(new ActionLogEvent($user, 'Medical license removed', $this->admin));
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            \LogHelper::error($e, ['userId' => $user->id]);
            throw new \DomainException('Medical license deleting error');
        }
    }

    /**
     * @param IdentityEditRequest $request
     * @param User $user
     */
    public function identityEdit(IdentityEditRequest $request, User $user): void
    {
        try {
            $user->update([
                'phone' => $request->phone
            ]);
            $specialist = $user->specialist;
            $specialist->update([
                'driver_address' => $request->address,
                'driver_city' => $request->city,
                'driver_state' => $request->state,
                'driver_zip' => $request->zip,
                'driver_first_name' => $request->first_name,
                'driver_last_name' => $request->last_name,
                'driver_middle_name' => $request->middle_name,
                'dob' => $request->dob
            ]);
            $this->specialistRepository->setArea($specialist, $request->state, $request->city, $request->zip);
            $this->dispatcher->dispatch(new DLUpdated($specialist));
            $this->dispatcher->dispatch(new SpecialistGeocodeEvent($user->specialist));
            $this->dispatcher->dispatch(new ActionLogEvent($user, 'Identity edit in ssn page'));
        } catch (\Exception $e) {
            \LogHelper::error($e, ['userId' => $user->id]);
            throw new \DomainException('Identity edit error');
        }
    }

    /**
     * @param CheckRequest $request
     * @param User $user
     * @throws SSNIsAlreadyRegisteredException
     */
    public function checkSave(CheckRequest $request, User $user): void
    {
        $sameUsers = User::where([
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            ['id', '!=', $user->id]
        ])->get();
        try {
            foreach ($sameUsers as $sameUser) {
                if (!$sameUser->specialist || !$sameUser->specialist->ssn) {
                    continue;
                }
                $ssn = EncryptHelper::decrypt($sameUser->specialist->ssn);
                if ($ssn == $request->ssn) {
                    $provider = $user->specialist;
                    $provider->setDuplicateStatus();
                    $provider->save();
                    throw new SSNIsAlreadyRegisteredException();
                }
            }
            if ($request->ssn === $user->specialist->ssnVal) {
                return;
            }
            $user->specialist->update([
                'ssn' => EncryptHelper::encrypt($request->ssn),
            ]);
            try {
                $this->ssnService->setSSN($user->uuid, $request->ssn);
            } catch (\DomainException $e) {
                //not very important so logged at the service
            }
            //$this->dispatcher->dispatch(new CreateReport($user));
            $this->dispatcher->dispatch(new ActionLogEvent($user, 'SSN saved'));
        } catch (SSNIsAlreadyRegisteredException $e) {
            throw new SSNIsAlreadyRegisteredException();
        } catch (\Exception $e) {
            \LogHelper::error($e, ['userId' => $user->id]);
            throw new \DomainException('SNN saving error');
        }
    }

    /**
     * @param User $user
     */
    public function lastStep(User $user): void
    {
        $user->update([
            'tmp_token' => null,
            'signup_step' => null
        ]);
        if (!$user->wallet) {
            $clientId = $this->walletService->createClient(
                $user->first_name,
                $user->last_name,
                null,
                $user->email,
                $user->created_at->format('Y-m-d H:i:s')
            );
            $user->createWallet($clientId);
            $user->wallet->save();
        }
        $this->dispatcher->dispatch(new ActionLogEvent($user, 'Registration last page'));
        $this->dispatcher->dispatch(new SuccessfulRegistrationEvent($user, route('account-details')));
    }
}
