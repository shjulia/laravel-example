<?php

declare(strict_types=1);

namespace App\UseCases\Auth\Practice;

use App\Entities\User\Practice\Practice;
use App\Entities\User\Practice\PracticeAddress;
use App\Entities\User\User;
use App\Events\Maps\Geocoder\PracticeAddressGeocodeEvent;
use App\Events\User\ActionLogEvent;
use App\Helpers\S3Helper;
use App\Http\Requests\Auth\Practice\BaseInfoRequest;
use App\Http\Requests\Auth\Practice\Details\BaseDetailsRequest;
use App\Http\Requests\Auth\Practice\Details\SecondaryDetailsRequest;
use App\Http\Requests\Auth\Practice\Details\TeamMemberRequest;
use App\Http\Requests\Auth\Practice\Details\ToolRequest;
use App\Http\Requests\General\PhotoRequest;
use App\Repositories\User\RolesRepository;
use App\Repositories\User\UserRepository;
use App\Services\Wallet\Practice\WalletService;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Class DetailsService
 * Save practice details such as photo, address, name, billing info.
 *
 * @package App\UseCases\Auth\Practice
 */
class DetailsService
{
    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @var RolesRepository
     */
    private $rolesRepository;
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
     * @param UserRepository $userRepository
     * @param RolesRepository $rolesRepository
     * @param Dispatcher $dispatcher
     * @param WalletService $walletService
     */
    public function __construct(
        UserRepository $userRepository,
        RolesRepository $rolesRepository,
        Dispatcher $dispatcher,
        WalletService $walletService
    ) {
        $this->userRepository = $userRepository;
        $this->rolesRepository = $rolesRepository;
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
        /** @var Practice $practice */
        $practice = $user->practice;
        if (!$request->hasFile('file')) {
            throw new \DomainException('Photo not found');
        }
        try {
            $path = S3Helper::uploadImage($request->file('file'));
            $practice->update([
                'practice_photo' => $path
            ]);
            $this->dispatcher->dispatch(
                new ActionLogEvent($user, 'Practice avatar updated (' . $path . ')', $this->admin)
            );
        } catch (\Exception $e) {
            \LogHelper::error($e, ['userId' => $user->id]);
            throw  new \DomainException('Photo saving error');
        }
        return S3Helper::getUrlByPath($path);
    }

    /**
     * @param BaseDetailsRequest $request
     * @param User $user
     */
    public function saveDetails(BaseDetailsRequest $request, User $user): void
    {
        try {
            $user->practice->update([
                'culture' => $request->culture,
                'notes' => $request->notes,
                'on_site_contact' => $request->on_site_contact
            ]);
            $this->dispatcher->dispatch(
                new ActionLogEvent($user, 'Practice details first screen updated', $this->admin)
            );
        } catch (\Exception $e) {
            \LogHelper::error($e, ['userId' => $user->id]);
            throw new \DomainException('Practice base info saving error');
        }
    }

    /**
     * @param SecondaryDetailsRequest $request
     * @param User $user
     */
    public function saveSecondaryDetails(SecondaryDetailsRequest $request, User $user): void
    {
        try {
            $user->practice->update([
                'park' => $request->park,
                'door' => $request->door,
                'dress_code' => $request->dress_code,
                'info' => $request->info
            ]);
            $this->dispatcher->dispatch(
                new ActionLogEvent($user, 'Practice details second screen updated', $this->admin)
            );
        } catch (\Exception $e) {
            \LogHelper::error($e, ['userId' => $user->id]);
            throw new \DomainException('Practice secondary info saving error');
        }
    }

    /**
     * @param ToolRequest $request
     * @param User $user
     */
    public function saveTool(ToolRequest $request, User $user): void
    {
        try {
            $practice = $user->practice;
            $practice->tool_id = $request->tool;
            $practice->saveOrFail();
            $this->dispatcher->dispatch(
                new ActionLogEvent($user, 'Practice management software updated', $this->admin)
            );
        } catch (\Throwable $e) {
            \LogHelper::error($e, ['userId' => $user->id]);
            throw new \DomainException('Practice Management software info saving error');
        }
    }

    /**
     * @param TeamMemberRequest $request
     * @param User $user
     * @return User
     * @throws \Throwable
     */
    public function saveTeamMember(TeamMemberRequest $request, User $user): User
    {
        try {
            $data = [
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email
            ];
            $practice = $user->practice;
            $role = $this->rolesRepository->getPracticeRole();
            $this->dispatcher->dispatch(new ActionLogEvent($user, 'Practice team member added', $this->admin));
            return DB::transaction(function () use ($request, $user, $practice, $role, $data) {

                if ($request->user_id) {
                    $member = $this->userRepository->getById($request->user_id);
                    $member->update($data);
                    $member->practices()->updateExistingPivot($practice->id, ['practice_role' => $request->role]);
                } else {
                    /** @var User $member */
                    $member = User::make($data);
                    $member->setWaitingStatus();
                    $member->setRandomPassword();
                    $member->saveOrFail();
                    $member->practices()->attach($practice->id, ['practice_role' => $request->role]);
                    $member->roles()->attach($role->id);
                }
                return $practice->users()->where('users.id', $member->id)->first();
            });
        } catch (\Exception $e) {
            \LogHelper::error($e, ['userId' => $user->id]);
            throw new \DomainException('User saving error');
        }
    }

    /**
     * @param int $memberId
     * @param User $user
     */
    public function deleteTeamMember(int $memberId, User $user): void
    {
        if (!$member = $this->userRepository->getById($memberId)) {
            throw new \DomainException('Member not found');
        }
        try {
            $practice = $user->practice;
            $member->practices()->detach($practice->id);
            $this->dispatcher->dispatch(new ActionLogEvent($user, 'Team member remove', $this->admin));
        } catch (\Exception $e) {
            \LogHelper::error($e, ['userId' => $user->id]);
            throw new \DomainException('Member deleting error');
        }
    }

    /**
     * @param BaseInfoRequest $request
     * @param User $user
     */
    public function addLocation(BaseInfoRequest $request, User $user): void
    {
        $practice = $user->practice;
        if ($practice->addresses()->where(['address' => $request->address, 'city' => $request->city])->exists()) {
            throw new \DomainException('You have already added this location');
        }
        try {
            $practiceAddress = $practice->addresses()->create([
                'practice_name' => $request->name,
                'address' => $request->address,
                'city' => $request->city,
                'state' => $request->state,
                'zip' => $request->zip,
                'url' => $request->url,
                'practice_phone' => $request->phone
            ]);
            $this->dispatcher->dispatch(new PracticeAddressGeocodeEvent($practiceAddress));
            $this->dispatcher->dispatch(new ActionLogEvent($user, 'Practice added location'));
        } catch (\Exception $e) {
            \LogHelper::error($e, ['userId' => $user->id]);
            throw new \DomainException('Location adding error');
        }
    }

    /**
     * @param BaseInfoRequest $request
     * @param PracticeAddress $practiceAddress
     * @param User $user
     */
    public function editLocation(BaseInfoRequest $request, PracticeAddress $practiceAddress, User $user): void
    {
        /** @var Practice $practice */
        $practice = $user->practice;
        if (!$practice->addresses()->where(['id' => $practiceAddress->id])->exists()) {
            throw new \DomainException('You can\'t edit this location');
        }
        try {
            $practiceAddress->update([
                'practice_name' => $request->name,
                'address' => $request->address,
                'city' => $request->city,
                'state' => $request->state,
                'zip' => $request->zip,
                'url' => $request->url,
                'practice_phone' => $request->phone
            ]);
            $this->dispatcher->dispatch(new ActionLogEvent($user, 'Practice updated location ' . $practiceAddress->id));
        } catch (\Exception $e) {
            \LogHelper::error($e, ['userId' => $user->id]);
            throw new \DomainException('Location updating error');
        }
    }

    /**
     * @param BaseInfoRequest $request
     * @param User $user
     */
    public function editCurrentLocation(BaseInfoRequest $request, User $user): void
    {
        /** @var Practice $practice */
        $practice = $user->practice;
        try {
            $practice->update([
                'practice_name' => $request->name,
                'address' => $request->address,
                'city' => $request->city,
                'state' => $request->state,
                'zip' => $request->zip,
                'url' => $request->url,
                'practice_phone' => $request->phone
            ]);
            $this->dispatcher->dispatch(new ActionLogEvent($user, 'Practice updated current location'));
        } catch (\Exception $e) {
            \LogHelper::error($e, ['userId' => $user->id]);
            throw new \DomainException('Location updating error');
        }
    }

    /**
     * @param PracticeAddress $practiceAddress
     * @param User $user
     */
    public function removeLocation(PracticeAddress $practiceAddress, User $user): void
    {
        if ($practiceAddress->practice_id != $user->practice->id) {
            throw new \DomainException('You can\'t remove this location');
        }
        try {
            $practiceAddress->delete();
            $this->dispatcher->dispatch(new ActionLogEvent($user, 'Practice removed location'));
        } catch (\Exception $e) {
            \LogHelper::error($e, ['userId' => $user->id]);
            throw new \DomainException('Location removing error');
        }
    }

    /**
     * @param Request $request
     * @param User $user
     */
    public function billingSave(Request $request, User $user): void
    {
        if (!$request->token) {
            throw new \DomainException('Card data must be set');
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
        try {
            $this->walletService->attachPaymentMethod($user->wallet->wallet_client_id, $request->token);
            $user->markWalletAsPaymentDataSet();
            $user->wallet->save();
        } catch (\Exception $e) {
            \LogHelper::error($e, ['userId' => $user->id]);
            throw new \DomainException('Practice billing info creating error');
        }
        $this->dispatcher->dispatch(new ActionLogEvent($user, 'Billing method saved'));
    }
}
