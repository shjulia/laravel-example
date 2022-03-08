<?php

namespace App\Listeners\Shift;

use App\Entities\DTO\SmsDTO;
use App\Entities\Shift\Shift;
use App\Entities\User\Provider\Specialist;
use App\Entities\User\User;
use App\Events\Shift\NotifyNotAvailableProviders;
use App\Mail\Invite\InviteMail;
use App\Mail\Shift\AvailabelMail;
use App\Notifications\SmsNotification;
use App\Repositories\Shift\ShiftRepository;
use App\UseCases\Shift\MatchingService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

/**
 * Class NotifyNotAvailableProvidersListener
 * Sends notification to providers who are not available, but potentially can fit the upcoming shift.
 * Event {@see \App\Events\Shift\NotifyNotAvailableProviders}
 *
 * @package App\Listeners\Shift
 */
class NotifyNotAvailableProvidersListener implements ShouldQueue
{
    /**
     * @var MatchingService
     */
    private $matchingService;

    /**
     * NotifyNotAvailableProvidersListener constructor.
     * @param MatchingService $matchingService
     */
    public function __construct(MatchingService $matchingService)
    {
        $this->matchingService = $matchingService;
    }

    /**
     * @param NotifyNotAvailableProviders $event
     */
    public function handle(NotifyNotAvailableProviders $event): void
    {
        /** @var Shift $shift */
        $shift = $event->shift;
        $providersNA = $this->matchingService->getNotAvailabelProviders($shift);
        $providers = Specialist::whereIn('user_id', $providersNA)->with('user')->get();
        $shouldText = $shift->isShouldSendText();
        foreach ($providers as $provider) {
            Mail::to($provider->user->email)->send(new AvailabelMail($provider->user->full_name));
            if ($shouldText) {
                $provider->user->notify(new SmsNotification(new SmsDTO(
                    "Shifts are waiting for you. Change availability: ",
                    route('shifts.provider.index')
                )));
            }
        }
    }
}
