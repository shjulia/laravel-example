<?php

declare(strict_types=1);

namespace App\Console\Commands\Users\Provider;

use App\Entities\Notification\EmailMark;
use App\Entities\User\Provider\Specialist;
use App\Mail\Users\Provider\MissingOutShiftsMail;
use App\Repositories\User\SpecialistRepository;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

/**
 * Class MissingOutShifts
 * Notify providers if they do not respond for 3 shifts.
 *
 * @package App\Console\Commands\Users\Provider
 */
class MissingOutShifts extends Command
{
    /**
     * @var string
     */
    protected $signature = 'provider:notify-missing-out-shifts';

    /**
     * @var string
     */
    protected $description = 'Notify provider about no respond 3 shifts';

    /**
     * @var SpecialistRepository
     */
    private $specialistRepository;

    /**
     * MissingOutShifts constructor.
     * @param SpecialistRepository $specialistRepository
     */
    public function __construct(SpecialistRepository $specialistRepository)
    {
        parent::__construct();
        $this->specialistRepository = $specialistRepository;
    }

    public function handle()
    {
        $providers = $this->specialistRepository->findWhereALotOfNoRespondShifts();
        foreach ($providers as $provider) {
            try {
                $user = $provider->user;
                $sum = $this->getShiftsCostSum($provider);
                Mail::to($user->email)->send(
                    new MissingOutShiftsMail($provider, $sum, $provider->shiftInvites->count())
                );
                $emailMark = EmailMark::createMark($user, EmailMark::MISSING_OUT_SHIFTS);
                $emailMark->saveOrFail();
            } catch (\Throwable $e) {
                \LogHelper::error($e);
                continue;
            }
        }
    }

    /**
     * @param Specialist $provider
     * @return float
     */
    private function getShiftsCostSum(Specialist $provider): float
    {
        $sum = 0;
        foreach ($provider->shiftInvites as $invite) {
            $sum += $invite->shift->cost;
        }
        return $sum;
    }
}
