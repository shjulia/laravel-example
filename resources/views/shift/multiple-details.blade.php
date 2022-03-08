@extends('layouts.main')

@section('content')
    <shift-multiple-details
        inline-template
        :shift="{{ $shift }}"
        v-cloak
    >
        <div class="hire hire-center shift-result-hire">
            <div class="centralform long">
                <div class="rel">
                    @if ($shift->freeChildren->count())
                        <div class="abs-white waiting-div">
                            <p><i class="fa fa-search"></i> Searching for a provider…</p>
                            <div id="movingBallG">
                                <div class="movingBallLineG"></div>
                                <div id="movingBallG_1" class="movingBallG"></div>
                            </div>
                        </div>
                    @endif
                    <gmap-map
                        :center="findCenter"
                        :zoom="zoom"
                        style="width: 100%; min-height:calc(100vh - 88px)"
                        :options="{gestureHandling: 'cooperative'}"
                        ref="map"
                    >
                        <gmap-marker
                            :position="findCenter"
                            :z-index="2"
                            icon="/img/map-marker-icon.png"

                        />
                    </gmap-map>
                    <div class="abs-white details-div container">
                        <div>
                            <h5 class="text-center"><i class="fa fa-calendar" aria-hidden="true"></i> {{ $shift->period() }}</h5>
                            <hr/>
                            @foreach($shift->children as $child)
                                <div class="onechildmap row">
                                    <div class="col-10">
                                        <a
                                            href="{{ $child->isAcceptedByProviderStatus() ? route('shifts.details', $child) : route('shifts.result', $child)  }}"
                                            class="childa"
                                        >
                                            <i class="fa fa-calendar" aria-hidden="true"></i>
                                            <b>Day {{ $loop->index + 1 }}: </b>
                                            {{ $child->period() }}
                                            @if($child->lunch_break)
                                                <span>({{ $child->lunch_break }} min. lunch)</span>
                                            @endif
                                            @if ($child->isAcceptedByProviderStatus())
                                                <span class="person">
                                                    {{ $child->provider->user->first_name  . ' ' . $child->provider->user->last_name }}
                                                </span>
                                            @else
                                                <span class="person">{{ $child->statusName() }}</span>
                                            @endif
                                        </a>
                                    </div>
                                    <div class="col-2">
                                        <a href="{{$child->isAcceptedByProviderStatus() ? route('shifts.details', $child) :route('shifts.result', $child)}}" class="btn btn-primary btn-sm">details</a>
                                    </div>
                                    <div class="col-12">
                                        <p class="date mb-2"></p>
                                    </div>
                                </div>
                            @endforeach
                            <div class="row mt-4">
                                <div class="col" v-if="canCancel()">
                                    <button type="button" class="btn btn-danger btn-big" @click="cancel()">Cancel Shift</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @include("shift._reason-modal")
        </div>

    </shift-multiple-details>
@endsection
