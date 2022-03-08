<?php

declare(strict_types=1);

namespace App\UseCases\Auth\Provider;

use App\Entities\User\Provider\Specialist;
use App\Entities\User\User;
use App\Events\Maps\Geocoder\SpecialistGeocodeEvent;
use App\Events\User\ActionLogEvent;
use App\Helpers\S3Helper;
use App\Http\Requests\Auth\Provider\DetailsRequest;
use App\Http\Requests\Auth\Provider\Onboarding\PaymentRequest;
use App\Http\Requests\General\PhotoRequest;
use App\Repositories\User\SpecialistRepository;
use App\Services\Wallet\Provider\WalletService;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Support\Facades\DB;

/**
 * Class DetailsService
 * Save provider details such as photo, address, availability, billing info.
 *
 * @package App\UseCases\Auth\Provider
 */
class DetailsService
{
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
     * @var WalletService
     */
    private $walletService;

    /**
     * DetailsService constructor.
     * @param SpecialistRepository $specialistRepository
     * @param Dispatcher $dispatcher
     * @param WalletService $walletService
     */
    public function __construct(
        SpecialistRepository $specialistRepository,
        Dispatcher $dispatcher,
        WalletService $walletService
    ) {
        $this->specialistRepository = $specialistRepository;
        $this->dispatcher = $dispatcher;
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
     * @param PhotoRequest $request
     * @param User $user
     * @return string
     */
    public function savePhoto(PhotoRequest $request, User $user): string
    {
        /** @var Specialist $specialist */
        $specialist = $user->specialist;
        if (!$request->hasFile('file')) {
            throw new \DomainException('Photo not found');
        }
        try {
            $path = S3Helper::uploadImage($request->file('file'));
            $specialist->update([
                'photo' => $path
            ]);
            $this->dispatcher->dispatch(new ActionLogEvent($user, 'Avatar set (' . $path . ')', $this->admin));
        } catch (\Exception $e) {
            \LogHelper::error($e, ['userId' => $user->id]);
            throw  new \DomainException('Photo saving error');
        }
        return S3Helper::getUrlByPath($path);
    }

    /**
     * @param DetailsRequest $request
     * @param User $user
     * @throws \Exception
     */
    public function saveDetails(DetailsRequest $request, User $user): void
    {
        /** @var Specialist $specialist */
        $specialist = $user->specialist;

        DB::beginTransaction();
        try {
            $user->update([
                'tmp_token' => null
            ]);

            $changes = $specialist->specialities()->sync($request->specialities ?: []);
            if (!empty($changes['attached']) || !empty($changes['detached'])) {
                $this->dispatcher->dispatch(new ActionLogEvent($user, 'Specialities updated', $this->admin));
            }

            $changes = $specialist->routineTasks()->sync($request->routine_tasks);
            if (!empty($changes['attached']) || !empty($changes['detached'])) {
                $this->dispatcher->dispatch(new ActionLogEvent($user, 'Routine tasks updated', $this->admin));
            }

            if (
                $request->state != $specialist->driver_state
                || $request->city != $specialist->driver_city
                || $request->zip != $specialist->driver_zip
                || $request->address != $specialist->driver_address
            ) {
                $specialist->update([
                    'driver_state' => $request->state,
                    'driver_city' => $request->city,
                    'driver_zip' => $request->zip,
                    'driver_address' => $request->address
                ]);
                $this->specialistRepository->setArea($specialist, $request->state, $request->city, $request->zip);
                $this->dispatcher->dispatch(new SpecialistGeocodeEvent($specialist));
                $this->dispatcher->dispatch(new ActionLogEvent($user, 'Address changed', $this->admin));
            }
            if ($request->delete) {
                $specialist->availabilities()->delete();
                foreach ($request->from ?? [] as $key => $from) {
                    if (!isset($request->from[$key]) || !isset($request->to[$key]) || !isset($request->day[$key])) {
                        continue;
                    }
                    foreach (explode(',', (string)$request->day[$key]) as $day) {
                        if (!in_array($day, [1,2,3,4,5,6,7])) {
                            continue;
                        }
                        $specialist->availabilities()->create(
                            [
                                'from_hour' => $request->from[$key],
                                'to_hour' => $request->to[$key],
                                'day' => (int)$day
                            ]
                        );
                    }
                }
                if (!$specialist->availabilities->isEmpty()) {
                    $specialist->update(['available' => 1]);
                }
                $this->dispatcher->dispatch(new ActionLogEvent($user, 'Availability changed', $this->admin));
            }
            $availabilityHolidays = array_keys($request->holiday ?? []);
            $changes = $specialist->holidays()->sync($availabilityHolidays);
            if (!empty($changes['attached']) || !empty($changes['detached'])) {
                $this->dispatcher->dispatch(new ActionLogEvent($user, 'Holidays updated', $this->admin));
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            \LogHelper::error($e, ['userId' => $user->id]);
            throw new \DomainException('Additional data saving error');
        }

        try {
            $this->changeTransferData($user, $request->routing_number, $request->account_number);
        } catch (\Exception $e) {
            \LogHelper::error($e, ['userId' => $user->id]);
            throw new \DomainException('Payment data error');
        }
    }

    /**
     * @param User $user
     * @param string|null $routineNumber
     * @param string|null $accountNumber
     */
    private function changeTransferData(User $user, ?string $routineNumber, ?string $accountNumber): void
    {
        if (!$routineNumber || !$accountNumber) {
            return;
        }
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
        $user->refresh();
        $this->walletService->attachTransferData($user->wallet->wallet_client_id, $routineNumber, $accountNumber);
        $user->markWalletAsTransferDataSet();
        $user->wallet->save();
        $this->dispatcher->dispatch(new ActionLogEvent($user, 'Payment info set', $this->admin));
    }

    /**
     * @param $user
     * @param PaymentRequest $request
     */
    public function setPayment($user, PaymentRequest $request): void
    {
        /** @var Specialist $provider */
        $provider = $user->specialist;
        if ($request->routing_number && $request->account_number) {
            $this->changeTransferData($user, $request->routing_number, $request->account_number);
        }
        if ($request->is_expedited) {
            $provider->setExpeditedPaymentStatus();
        } else {
            $provider->setStandardPaymentStatus();
        }
        $provider->save();
        $this->dispatcher->dispatch(new ActionLogEvent($user, 'payment info changed'));
    }

    /**
     * @param Specialist $provider
     */
    public function changeAvailability(Specialist $provider): void
    {
        $provider->update(['available' => !$provider->available]);
    }
}
