<?php

namespace App\Providers;

use App\Events\Admin\Areas\AreaEvent;
use App\Events\Invite\InviteEvent;
use App\Events\Mail\MessageIdTranscriptEvent;
use App\Events\Maps\Geocoder\PracticeAddressGeocodeEvent;
use App\Events\Maps\Geocoder\PracticeGeocodeEvent;
use App\Events\Maps\Geocoder\SpecialistGeocodeEvent;
use App\Events\Maps\PlacePhotoEvent;
use App\Events\Maps\TimeZoneEvent;
use App\Events\Shift\AlmostFinishedShiftEvent;
use App\Events\Shift\NotifyNotAvailableProviders;
use App\Events\Shift\Provider\AcceptShiftEvent as AcceptShiftProviderEvent;
use App\Events\Shift\AcceptShiftEvent;
use App\Events\Shift\PaymentEvent;
use App\Events\Shift\ProvidersNotFoundEvent;
use App\Events\Shift\ShiftCanceledEvent;
use App\Events\Shift\ShiftUpdateEvent;
use App\Events\Shift\Support\ProviderRequestedEvent;
use App\Events\User\Provider\AccountApproved as AccountApprovedProvider;
use App\Events\User\Practice\AccountApproved as AccountApprovedPractice;
use App\Events\User\AccountRejected;
use App\Events\User\ActionLogEvent;
use App\Events\User\LoginEvent;
use App\Events\User\PartnerRegisterEvent;
use App\Events\User\Provider\DLUpdated;
use App\Events\User\SetPasswordEvent;
use App\Events\User\SuccessfulRegistrationEvent;
use App\Listeners\Area\AreaListener;
use App\Listeners\Invite\InviteListener;
use App\Listeners\Maps\Geocoder\PracticeAddressGeocodeListener;
use App\Listeners\Maps\Geocoder\PracticeGeocodeListener;
use App\Listeners\Maps\Geocoder\SpecialistGeocodeListener;
use App\Listeners\Maps\PlacePhotoListener;
use App\Listeners\Maps\TimeZoneListener;
use App\Listeners\Shift\AcceptShiftListener;
use App\Listeners\Shift\AlmostFinishedShiftListener;
use App\Listeners\Shift\PaymentListener;
use App\Listeners\Shift\NotifyNotAvailableProvidersListener;
use App\Listeners\Shift\Provider\AcceptShiftListener as AcceptShiftProviderListener;
use App\Listeners\Shift\ProvidersNotFoundListener;
use App\Listeners\Shift\ShiftCanceledListener;
use App\Listeners\Shift\ShiftUpdateListener;
use App\Listeners\Shift\Support\ProviderRequestedListener;
use App\Listeners\User\Provider\AccountApprovedListener as AccountApprovedProviderListener;
use App\Listeners\User\Practice\AccountApprovedListener as AccountApprovedPracticeListener;
use App\Listeners\User\AccountRejectedListener;
use App\Listeners\User\ActionLogListener;
use App\Listeners\User\LoginListener;
use App\Listeners\User\PartnerRegisterListener;
use App\Listeners\User\Provider\DLUpdatedListener;
use App\Listeners\User\SetPasswordListener;
use App\Listeners\User\SuccessfulRegistrationListener;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Mail\Events\MessageSent;

/**
 * Class EventServiceProvider
 * @package App\Providers
 */
class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        AccountApprovedProvider::class => [
            AccountApprovedProviderListener::class
        ],
        AccountApprovedPractice::class => [
            AccountApprovedPracticeListener::class
        ],
        PlacePhotoEvent::class => [
            PlacePhotoListener::class
        ],
        InviteEvent::class => [
            InviteListener::class
        ],
        AcceptShiftEvent::class => [
            AcceptShiftListener::class
        ],
        AcceptShiftProviderEvent::class => [
            AcceptShiftProviderListener::class
        ],
        PracticeGeocodeEvent::class => [
            PracticeGeocodeListener::class
        ],
        SpecialistGeocodeEvent::class => [
            SpecialistGeocodeListener::class
        ],
        SetPasswordEvent::class => [
            SetPasswordListener::class
        ],
        SuccessfulRegistrationEvent::class => [
            SuccessfulRegistrationListener::class
        ],
        PartnerRegisterEvent::class => [
            PartnerRegisterListener::class
        ],
        NotifyNotAvailableProviders::class => [
            NotifyNotAvailableProvidersListener::class
        ],
        ProvidersNotFoundEvent::class => [
            ProvidersNotFoundListener::class
        ],
        PaymentEvent::class => [
            PaymentListener::class
        ],
        MessageSent::class => [
            MessageIdTranscriptEvent::class
        ],
        ActionLogEvent::class => [
            ActionLogListener::class
        ],
        ProviderRequestedEvent::class => [
            ProviderRequestedListener::class
        ],
        AreaEvent::class => [
            AreaListener::class
        ],
        PracticeAddressGeocodeEvent::class => [
            PracticeAddressGeocodeListener::class
        ],
        AccountRejected::class => [
            AccountRejectedListener::class
        ],
        ShiftCanceledEvent::class => [
            ShiftCanceledListener::class
        ],
        ShiftUpdateEvent::class => [
            ShiftUpdateListener::class
        ],
        AlmostFinishedShiftEvent::class => [
            AlmostFinishedShiftListener::class
        ],
        TimeZoneEvent::class => [
            TimeZoneListener::class
        ],
        LoginEvent::class => [
            LoginListener::class
        ],
        DLUpdated::class => [
            DLUpdatedListener::class
        ]
        /*'Illuminate\Mail\Events\MessageSent' => [
            'App\Events\Mail\MessageIdTranscript'
        ],*/
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
