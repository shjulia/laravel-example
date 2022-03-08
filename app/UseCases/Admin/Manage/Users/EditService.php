<?php

declare(strict_types=1);

namespace App\UseCases\Admin\Manage\Users;

use App\Entities\Industry\Rate;
use App\Entities\Invite\Invite;
use App\Entities\User\Practice\Practice;
use App\Entities\User\User;
use App\Events\Maps\Geocoder\PracticeGeocodeEvent;
use App\Events\Maps\Geocoder\SpecialistGeocodeEvent;
use App\Events\Maps\PlacePhotoEvent;
use App\Events\User\ActionLogEvent;
use App\Http\Requests\Admin\User\Edit\InviteEditRequest;
use App\Http\Requests\Admin\User\Edit\PositionRequest;
use App\Http\Requests\Admin\User\Edit\UserDataRequest;
use App\Http\Requests\Admin\User\UpdateRequest;
use App\Http\Requests\Auth\Practice\BaseInfoRequest;
use App\Repositories\Data\RateRepository;
use App\Repositories\Industry\PositionRepository;
use App\Repositories\User\SpecialistRepository;
use Carbon\Carbon;
use Illuminate\Contracts\Events\Dispatcher;

/**
 * Class EditService
 * Manage User data, position, ssn, rate and invite.
 *
 * @package App\UseCases\Admin\Manage\Users
 */
class EditService
{
    /**
     * @var PositionRepository
     */
    private $positionRepository;

    /**
     * @var SpecialistRepository
     */
    private $specialistRepository;

    /**
     * @var Dispatcher
     */
    private $dispatcher;
    /**
     * @var RateRepository
     */
    private $rateRepository;

    /**
     * EditService constructor.
     * @param PositionRepository $positionRepository
     * @param SpecialistRepository $specialistRepository
     * @param RateRepository $rateRepository
     * @param Dispatcher $dispatcher
     */
    public function __construct(
        PositionRepository $positionRepository,
        SpecialistRepository $specialistRepository,
        RateRepository $rateRepository,
        Dispatcher $dispatcher
    ) {
        $this->positionRepository = $positionRepository;
        $this->specialistRepository = $specialistRepository;
        $this->dispatcher = $dispatcher;
        $this->rateRepository = $rateRepository;
    }

    /**
     * @param UserDataRequest $request
     * @param User $user
     * @param User $admin
     */
    public function editUserData(UserDataRequest $request, User $user, User $admin): void
    {
        try {
            $user->update([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'phone' => $request->phone,
                'email' => $request->email,
                'password' => $request->password ? bcrypt($request->password) : $user->password
            ]);
            $this->dispatcher->dispatch(new ActionLogEvent($user, 'Edit base user data', $admin));
        } catch (\Exception $e) {
            throw new \DomainException('User info saving error');
        }
    }

    /**
     * @param PositionRequest $request
     * @param User $user
     * @param User $admin
     */
    public function editPosition(PositionRequest $request, User $user, User $admin): void
    {
        $position = $this->positionRepository->getById((int)$request->position);
        try {
            $user->specialist->update([
                'industry_id' => $position->industry->id,
                'position_id' => $position->id
            ]);
            $this->dispatcher->dispatch(new ActionLogEvent($user, 'Position edit (' . $position->id . ')', $admin));
        } catch (\Exception $e) {
            throw new \DomainException('Provider info saving error');
        }
    }

    /**
     * @param string|null $ssn
     * @param User $user
     * @param User $admin
     */
    public function editSsn(?string $ssn, User $user, User $admin): void
    {
        if (!$ssn) {
            throw new \DomainException('SSN is required');
        }
        try {
            $user->specialist->update([
                'ssn' => $ssn
            ]);
            $this->dispatcher->dispatch(new ActionLogEvent($user, 'Edit provider ssn', $admin));
        } catch (\Exception $e) {
            throw new \DomainException('Provider info saving error');
        }
    }

    /**
     * @param Practice $practice
     * @param int|null $rateId
     * @param User $admin
     */
    public function addRateToPractice(Practice $practice, ?int $rateId, User $admin): void
    {
        try {
            if ($rateId) {
                $rate = $this->rateRepository->getById($rateId);
                $practice->setRate($rate);
            } else {
                $practice->removeRate();
            }
            $practice->saveOrFail();
            $this->dispatcher->dispatch(new ActionLogEvent($practice->practiceCreator(), 'Rate changed', $admin));
        } catch (\Throwable $e) {
            throw new \DomainException('Practice updating error');
        }
    }

    /**
     * @param Invite $invite
     * @param InviteEditRequest $request
     */
    public function editInvite(Invite $invite, InviteEditRequest $request): void
    {
        try {
            $invite->editPayment(
                $request->payment_system,
                $request->status,
                $request->charge_id
            );
            $invite->saveOrFail();
        } catch (\Throwable $e) {
            throw new \DomainException('Saving error');
        }
    }
}
