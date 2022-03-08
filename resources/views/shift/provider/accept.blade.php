@extends('layouts.main')

@section('content')
<accept
    v-cloak
    accept-route="{{ route('shifts.provider.accept', $shift) }}"
    view-invite-url="{{ route('shifts.provider.viewInvite', $shift) }}"
    success-redirect-route="{{ route('shifts.provider.index') }}"
    free-children-amount="{{ $shift->freeChildren->count() }}"
    :first-child="{{ $shift->freeChildren[0] ?? 'null' }}"
    :shift="{{ $shift }}"
    inline-template
>
    <div v-cloak>
        <gmap-map
                :center="{{ collect(['lat' => $shift->practice_location->lat, 'lng' => $shift->practice_location->lng]) }}"
                :zoom="16"
                ref="map"
                style="width: 100%; min-height:calc(100vh - 107px)"
        >
        </gmap-map>
        <div class="accept-block">
            <div class="background"></div>

            <section class="card">
                <img src="{{ $practice->practice_photo ? $practice->practice_photo_url : '/img/anonim.jpg' }}">
                <div class="info"
                     v-if="!isAccepted && !isMultipleDeclined"
                >
                    <h5>{{ $shift->practice_location->practiceName }}</h5>
                    <p class="address">{{ $shift->practice_location->fullAddress() }}</p>
                    @if (!$shift->multi_days)
                        <div class="date">
                            <div class="w-100">
                                <i class="fa fa-calendar" aria-hidden="true"></i>
                                <b>{{ $shift->period() }}</b>
                                <b>{{ ' ($' . $shift->cost_without_surge . (($shift->surge_price || $shift->bonus) ? (' plus $' . ($shift->surge_price + $shift->bonus) . ' bonus.') : '') . ')' }}</b>
                                @if($shift->lunch_break)
                                    <p>(including {{ $shift->lunch_break }} min. lunch break)</p>
                                @endif
                            </div>
                            {{--<div class="w-50 text-right">
                                <i class="fa fa-clock-o" aria-hidden="true"></i>
                                <b>{{ $shift->from_time . ' - ' . $shift->to_time }}</b>
                            </div>--}}
                        </div>
                    @else
                        <div class="date-details">
                            @foreach($shift->freeChildren as $child)
                                <div class="date">
                                    <div class="w-100">
                                        <i class="fa fa-calendar" aria-hidden="true"></i>
                                        <b>Day {{ $loop->index + 1 }}: </b>
                                        {{ $child->period() }}
                                        <b>{{ ' ($' . $child->cost_without_surge . (($child->surge_price + $child->bonus) ? (' plus $' . ($child->surge_price + $child->bonus) . ' bonus.') : '') . ')' }}</b>
                                        @if($child->lunch_break)
                                            <p class="mb-0">(including {{ $child->lunch_break }} min. lunch break)</p>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                    <div class="contacts only-mobile">
                        <a href="{{'sms:' . $shift->practice_location->practicePhone }}" class="message">
                            <i class="fa fa-comment-o" aria-hidden="true"></i>
                            Message
                        </a>
                        <a href="{{'tel:' . $shift->practice_location->practicePhone }}" class="call">
                            <i class="fa fa-phone" aria-hidden="true"></i>
                            Call
                        </a>
                    </div>
                    <p class="will-pay">
                        @if ($shift->multi_days)
                            We will pay you <b>${{ $shift->freeCost() }}</b>
                            @if ($shift->multi_days)
                                for <b>{{ $shift->freeChildren->count() }}</b> days
                            @endif
                        @else
                            We will pay you <b>${{ $shift->cost }}</b>
                        @endif
                    </p>
                </div>
                <div class="buttons"
                     v-if="!isAccepted && !isMultipleDeclined"
                >
                    <button class="accept" @click="accept">Accept</button>
                    @if (!$shift->multi_days)
                        <a href="{{ route('shifts.provider.decline', $shift->id) }}">
                            <button class="decline">Decline</button>
                        </a>
                    @else
                        <button class="decline" @click="declineMultiple()">Decline</button>
                    @endif
                </div>

                {{--show after accept--}}
                <div
                        v-if="isAccepted"
                        class="accepted-info"
                >
                    <h5 class="text-center">Thank You</h5>
                    <div class="buttons">
                        <a
                                href="#"
                                class="accept"
                                @click.prevent="leaving('now')"
                        >I'm Leaving Now</a>
                        <a
                                href="#"
                                class="accept"
                                @click.prevent="leaving('later')"
                        >I'm leaving Now within 15 minutes</a>

                        <a href="{{ route('shifts.provider.decline', $shift->id) }}" class="cancel-link">I cannot leave in time</a>
                    </div>
                </div>
                {{--!--}}

                {{--show after accept--}}
                <div
                    v-if="isMultipleDeclined"
                    class="accepted-info"
                >
                    <h5 class="text-center">Are you available to work any of the days?</h5>
                    <div class="buttons"
                    >
                        <form action="{{ route('shifts.provider.multipleAccept', $shift->id) }}" method="POST">
                            @csrf
                            @foreach($shift->freeChildren as $child)
                                <div class="date">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" id="d{{ $loop->index + 1 }}" name="shifts[]" value="{{ $child->id }}">
                                        <label class="custom-control-label" for="d{{ $loop->index + 1 }}">
                                            <b>Day {{ $loop->index + 1 }}: </b>
                                            {{ $child->period() }}
                                            @if($child->lunch_break)
                                                <p class="mb-0">(including {{ $child->lunch_break }} min. lunch break)</p>
                                            @endif
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                            <p></p>
                            <button class="accept" type="submit">Accept</button>
                        </form>

                        <a href="{{ route('shifts.provider.decline', $shift->id) }}">
                            <button class="decline">Decline anyway</button>
                        </a>
                    </div>
                </div>
                {{--!--}}

            </section>
        </div>
    </div>
</accept>
@endsection
