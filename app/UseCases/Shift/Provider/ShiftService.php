<?php

declare(strict_types=1);

namespace App\UseCases\Shift\Provider;

use App\Entities\Shift\Shift;
use App\Entities\Shift\ShiftInvite;
use App\Entities\Shift\ShiftTracking;
use App\Entities\User\Provider\Specialist;
use App\Events\Shift\Provider\AcceptShiftEvent;
use App\Events\Shift\Provider\DeclineShiftEvent;
use App\Events\Shift\ShiftUpdateEvent;
use App\Http\Requests\Shift\Provider\MultiDayRequest;
use App\Jobs\Shift\DayBeforeFirstShiftJob;
use App\Jobs\Shift\FinishShiftJob;
use App\Repositories\Shift\ShiftRepository;
use App\Repositories\User\SpecialistRepository;
use App\UseCases\Shift\CostService;
use App\UseCases\Shift\ShiftPaymentService;
use DomainException;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Class ShiftService
 * Provides actions with shift. Allows to start, finish, accept or decline shift.
 *
 * @package App\UseCases\Shift\Provider
 */
class ShiftService
{
    /**
     * @var ShiftRepository
     */
    private $shiftRepository;

    /**
     * @var SpecialistRepository
     */
    private $specialistRepository;

    /**
     * @var Dispatcher
     */
    private $dispatcher;
    /**
     * @var ShiftPaymentService
     */
    private $shiftPaymentService;
    /**
     * @var CostService
     */
    private $costService;

    /**
     * ShiftService constructor.
     * @param ShiftRepository $shiftRepository
     * @param SpecialistRepository $specialistRepository
     * @param Dispatcher $dispatcher
     * @param ShiftPaymentService $shiftPaymentService
     * @param CostService $costService
     */
    public function __construct(
        ShiftRepository $shiftRepository,
        SpecialistRepository $specialistRepository,
        Dispatcher $dispatcher,
        ShiftPaymentService $shiftPaymentService,
        CostService $costService
    ) {
        $this->shiftRepository = $shiftRepository;
        $this->specialistRepository = $specialistRepository;
        $this->dispatcher = $dispatcher;
        $this->shiftPaymentService = $shiftPaymentService;
        $this->costService = $costService;
    }

    /**
     * @param Shift $shift
     * @param Specialist $specialist
     */
    public function check(Shift $shift, Specialist $specialist): void
    {
        if ($shift->isCanceledStatus() || $shift->isArchived()) {
            throw new \DomainException('Practice already canceled shift');
        }
        if ($shift->isNoPrividerFoundStatus()) {
            throw new \DomainException('You could not accept past shift');
        }
        if (
            $shift->multi_days
            && $shift->freeChildren->isEmpty()
            && !in_array($specialist->user_id, $shift->children->pluck('provider_id')->toArray())
        ) {
            throw new \DomainException('Another provider has already accepted this shift. You did not have time');
        }
        if ($shift->provider_id && $shift->provider_id != $specialist->user_id) {
            throw new \DomainException('Another provider has already accepted this shift. You did not have time');
        }
        $this->shiftRepository->getShiftInvite($shift, $specialist->user_id);
        if ($shift->isAcceptedByProviderStatus() || $shift->isFinishedStatus()) {
            throw new \DomainException('Shift already accepted');
        }
    }

    /**
     * @param Shift $shift
     * @param $request
     * @param Specialist $provider
     */
    public function accept(Shift $shift, $request, Specialist $provider): void
    {
        $shift->answer = $request->param['answer'];

        $shift = $this->setProviderToShift($shift, $shift, $provider);

        $provider = $this->specialistRepository->getWithUser($provider);
        $this->dispatcher->dispatch(
            new AcceptShiftEvent($shift->practice->id, $provider, $shift->id, $shift->creator_id)
        );
        $this->dispatcher->dispatch(new ShiftUpdateEvent(
            $shift,
            'Provider accepted shift',
            $provider->user,
            true
        ));

        DayBeforeFirstShiftJob::dispatch($shift, $provider);

        if (!$shift->multi_days) {
            $this->recharge($shift);
            return;
        }
        /** @var Shift $child */
        foreach ($shift->freeChildren as $child) {
            if ($child->isAcceptedByProviderStatus()) {
                continue;
            }
            $this->setProviderToShift($child, $shift, $provider);
            $this->recharge($shift);
        }
    }

    /**
     * @param Shift $shift
     */
    private function recharge(Shift $shift): void
    {
        if ($shift->multi_days || !$shift->is_floating) {
            return;
        }
        try {
            $this->shiftPaymentService->refundAndPay($shift, $shift->cost_for_practice);
        } catch (\Exception $e) {
            \LogHelper::error($e, ['message' => 'Recalculated charge error']);
        }
    }

    /**
     * @param Shift $shift
     * @param Shift $baseShift
     * @param Specialist $provider
     * @return Shift
     */
    private function setProviderToShift(Shift $shift, Shift $baseShift, Specialist $provider): Shift
    {
        $shift->assignProviderToShift($provider);
        if (!$shift->save()) {
            throw new \DomainException('Accepting error');
        }
        $invite = $this->shiftRepository->getShiftInvite($baseShift, $provider->user_id);
        $invite->accept();
        $invite->save();
        return $shift;
    }

    /**
     * @param Shift $shift
     * @param MultiDayRequest $request
     * @param Specialist $provider
     * @throws \Exception
     */
    public function multipleAccept(Shift $shift, MultiDayRequest $request, Specialist $provider): void
    {
        $accepted = false;
        DB::beginTransaction();
        try {
            foreach ($request->shifts as $shiftId) {
                $shiftPart = $this->shiftRepository->getByIdOnlyShift((int)$shiftId);
                if (!$shiftPart->isChildOf($shift) || $shiftPart->isAcceptedByProviderStatus()) {
                    continue;
                }
                $this->setProviderToShift($shiftPart, $shift, $provider);
                $accepted = true;
            }

            if (!$accepted) {
                return;
            }
            $this->checkNotAcceptedLeft($shift);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \DomainException('Accepting error');
        }


        $provider = $this->specialistRepository->getWithUser($provider);
        $this->dispatcher->dispatch(
            new AcceptShiftEvent($shift->practice->id, $provider, $shift->id, $shift->creator_id)
        );

        $this->dispatcher->dispatch(new ShiftUpdateEvent(
            $shift,
            'Provider accepted several shift days',
            $provider->user,
            true
        ));
    }

    /**
     * @param Shift $shift
     */
    public function checkNotAcceptedLeft(Shift $shift): void
    {
        $shift = $this->shiftRepository->getByIdOnlyShift($shift->id);
        if ($shift->freeChildren->count()) {
            return;
        }
        $shift->setAcceptedByProviderStatus();
        if (!$shift->save()) {
            throw new \DomainException('Accepting error');
        }
    }

    /**
     * @param Shift $shift
     * @param Specialist $provider
     */
    public function decline(Shift $shift, Specialist $provider): void
    {
        $invite = $this->shiftRepository->getShiftInvite($shift, $provider->user_id);
        $invite->decline();
        $invite->save();
        if ($shift->potential_provider_id == $provider->user_id) {
            $shift->potential_provider_id = null;
            if (!$shift->save()) {
                throw new \DomainException('Decline error');
            }
        }
        $this->dispatcher->dispatch(new ShiftUpdateEvent(
            $shift,
            'Provider declined shift',
            $provider->user
        ));
        //event(new DeclineShiftEvent($shift->practice->id, $shift->creator_id));
    }

    /**
     * @param Shift $shift
     * @param Specialist $provider
     */
    public function view(Shift $shift, Specialist $provider): void
    {
        $invite = $this->shiftRepository->getShiftInvite($shift, $provider->user_id);
        if (!$invite->isNoRespond()) {
            return;
        }
        $invite->setViewedStatus();
        $invite->save();
    }

    /**
     * @param Shift $shift
     * @param float|null $lat
     * @param float|null $lng
     */
    public function start(Shift $shift, ?float $lat, ?float $lng): void
    {
        $track = $this->shiftRepository->findStartTrack($shift);
        if ($track) {
            throw new \DomainException('You have already started shift');
        }
        $track = ShiftTracking::createTrack($shift, true, $lat, $lng);
        try {
            $track->saveOrFail();
        } catch (\Throwable $e) {
            throw new \DomainException('Shift starting error');
        }
    }
    /**
     * @param Shift $shift
     * @param float|null $lat
     * @param float|null $lng
     */
    public function finish(Shift $shift, ?float $lat, ?float $lng): void
    {
        $track = ShiftTracking::createTrack($shift, false, $lat, $lng);
        try {
            $track->saveOrFail();
            FinishShiftJob::dispatch($shift);
        } catch (\Throwable $e) {
            throw new \DomainException('Shift finishing error');
        }
    }

    /**
     * returns shifts dates
     *
     * @param $shifts
     * @return array
     */
    public function getShiftsDates($shifts): array
    {
        $shifts_dates = [];

        foreach ($shifts as $shift) {
            $shifts_dates[] = $shift->date;
        }

        return $shifts_dates;
    }
}
