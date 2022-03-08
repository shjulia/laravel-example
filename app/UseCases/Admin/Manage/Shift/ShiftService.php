<?php

namespace App\UseCases\Admin\Manage\Shift;

use App\Entities\Shift\Shift;
use App\Entities\Shift\ShiftInvite;
use App\Entities\User\Provider\Specialist;
use App\Entities\User\User;
use App\Events\Shift\Provider\AcceptShiftEvent;
use App\Events\Shift\AcceptShiftEvent as InviteToShiftEvent;
use App\Events\Shift\ShiftCanceledEvent;
use App\Repositories\Payment\ChargeRepository;
use App\Repositories\User\SpecialistRepository;
use Illuminate\Contracts\Events\Dispatcher;
use App\UseCases\Shift\ShiftService as PracticeShiftService;
use Illuminate\Support\Facades\DB;

/**
 * Class ShiftService
 * Manage shift: edit provider, invite provider manually, cancel, restart matching, archive and refund charge.
 *
 * @package App\UseCases\Admin\Manage\Shift
 */
class ShiftService
{
    /**
     * @var Dispatcher
     */
    private $dispatcher;
    /**
     * @var ChargeRepository
     */
    private $chargeRepository;
    /**
     * @var SpecialistRepository
     */
    private $specialistRepository;
    /**
     * @var PracticeShiftService
     */
    private $practiceShiftService;

    /**
     * ShiftService constructor.
     * @param Dispatcher $dispatcher
     * @param ChargeRepository $chargeRepository
     * @param SpecialistRepository $specialistRepository
     * @param PracticeShiftService $practiceShiftService
     */
    public function __construct(
        Dispatcher $dispatcher,
        ChargeRepository $chargeRepository,
        SpecialistRepository $specialistRepository,
        PracticeShiftService $practiceShiftService
    ) {
        $this->dispatcher = $dispatcher;
        $this->chargeRepository = $chargeRepository;
        $this->specialistRepository = $specialistRepository;
        $this->practiceShiftService = $practiceShiftService;
    }

    /**
     * @param Shift $shift
     * @param int $providerId
     * @throws \Exception
     */
    public function editProvider(Shift $shift, int $providerId): void
    {
        if ($shift->provider_id == $providerId) {
            return;
        }
        $provider = $this->specialistRepository->getById($providerId);
        DB::beginTransaction();
        try {
            $shift->assignProviderToShift($provider);
            $shift->save();

            if ($shift->multi_days) {
                /** @var Shift $child */
                foreach ($shift->children as $child) {
                    $child->assignProviderToShift($provider);
                    $child->save();
                }
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \DomainException($e->getMessage());
        }
        $shift->refresh();
        $this->dispatcher->dispatch(new AcceptShiftEvent(
            $shift->practice->id,
            !$shift->multi_days ? $shift->provider : $shift->children[0]->provider,
            $shift->id,
            $shift->creator_id
        ));
    }

    /**
     * @param Shift $shift
     * @param int $providerId
     */
    public function inviteProvider(Shift $shift, int $providerId): void
    {
        if ($shift->isHasProvider()) {
            throw new \DomainException('Provider have been already set.');
        }
        /** @var User $user */
        $user = User::where('id', $providerId)->first();
        /** @var Specialist $provider */
        $provider = $user->specialist;
        if (!$provider->isApproved()) {
            throw new \DomainException('Provider is not approved');
        }
        if ($shift->position_id !== $provider->position_id) {
            throw new \DomainException('Provider position is not suite for this shift');
        }
        try {
            $invite = ShiftInvite::newInvite($shift, $provider);
            $invite->saveOrFail();
        } catch (\Throwable $e) {
            throw new \DomainException('Inviting error');
        }
        $this->dispatcher->dispatch(new InviteToShiftEvent($user, $shift));
    }

    /**
     * @param Shift $shift
     */
    public function refundCharge(Shift $shift): void
    {
        $charge = $this->chargeRepository->getLastCharge($shift);
        if (!$charge) {
            return;
        }
        try {
            $this->practiceShiftService->refundCharge($shift);
        } catch (\Exception $e) {
            \LogHelper::error($e, ['userId' => $shift->creator->id]);
            throw new \DomainException($e->getMessage());
        }
    }

    /**
     * @param Shift $shift
     */
    public function cancel(Shift $shift): void
    {
        $charge = $this->chargeRepository->getLastCharge($shift);
        if ($shift->isCanceledStatus()) {
            throw new \DomainException('Shift have been already canceled');
        }
        try {
            $this->setShiftToCancel($shift);
            $this->checkAndCancelChildren($shift);
            if ($charge && !$charge->isRefund()) {
                $this->practiceShiftService->refundCharge($shift);
            }
        } catch (\Exception $e) {
            \LogHelper::error($e, ['userId' => $shift->creator->id]);
            throw new \DomainException($e->getMessage());
        }
    }

    /**
     * @param Shift $shift
     * @return Shift
     */
    private function setShiftToCancel(Shift $shift): Shift
    {
        $shift->setCanceledStatus();
        $shift->save();
        if ($shift->isHasProvider()) {
            $this->dispatcher->dispatch(new ShiftCanceledEvent($shift));
        }
        return $shift;
    }

    /**
     * @param Shift $shift
     */
    private function checkAndCancelChildren(Shift $shift): void
    {
        if (!$shift->multi_days) {
            return;
        }
        /** @var Shift $child */
        foreach ($shift->children as $child) {
            $this->setShiftToCancel($child);
        }
    }

    /**
     * @param Shift $shift
     * @throws \Exception
     */
    public function archive(Shift $shift): void
    {
        if ($shift->isArchived()) {
            throw new \DomainException('Shift is already archived');
        }
        if (!($shift->isCanceledStatus() || $shift->isCreatingStatus())) {
            throw new \DomainException('You can archive only canceled and not fully-created shifts.');
        }
        DB::beginTransaction();
        try {
            $shift->setArchivedStatus();
            $shift->save();
            if ($shift->multi_days) {
                /** @var Shift $child */
                foreach ($shift->children as $child) {
                    $child->setArchivedStatus();
                    $child->save();
                }
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \DomainException($e->getMessage());
        }
    }

    /**
     * @param Shift $shift
     */
    public function restartMatching(Shift $shift): void
    {
        if (!$shift->canBeReMatched()) {
            throw new \DomainException('You can\'t re-match this shift');
        }
        try {
            $shift->setCreatingStatus();
            $shift->provider_id = null;
            $shift->save();
            $shift->refresh();
            $this->practiceShiftService->startMatching($shift);
        } catch (\Exception $e) {
            throw new \DomainException('Restarting error.');
        }
    }
}
