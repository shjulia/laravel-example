@extends('layouts.main')

@section('content')
    <shift-details
            inline-template
            v-cloak
            :shift="{{ $shift }}"
            cancel-action="{{ route('shifts.cancel', $shift) }}"
            index-url="{{ route('shifts.index') }}"
    >
        <div>
            <div class="hire hire-center shift-result-hire">
                <div class="centralform long">
                    <div class="rel">
                        <gmap-map
                                :center="findCenter"
                                :zoom="zoom"
                                style="width: 100%; min-height:calc(100vh - 88px)"
                                :options="{gestureHandling: 'cooperative'}"
                                ref="map"
                        >
                            <div v-if="showProvider">
                                <gmap-marker
                                        :position="findProvider"
                                        :z-index="2"
                                        icon="/img/map-marker-icon-red.png"

                                />
                            </div>

                            <gmap-marker
                                    :position="findCenter"
                                    :z-index="2"
                                    icon="/img/map-marker-icon.png"

                            />
                        </gmap-map>
                        <div class="abs-white details-div container">
                            <div v-if="!showDetails">
                                <div class="row">
                                    <div class="col-md-2 col-3">
                                        <img class="circle-ava" src="{{ $shift->provider->photo ? $shift->provider->photo_url : '/img/anonim.jpg' }}" />
                                    </div>
                                    <div class="col-md-7 col-5">
                                        <p class="title">{{ $shift->provider->user->first_name . ' ' . $shift->provider->user->last_name }}</p>
                                        <p class="desc">{{ $shift->position->title }}</p>
                                    </div>
                                    <div class="col-md-3 col-4 text-right">
                                        <a href="{{'tel:' . $shift->provider->user->phone }}" class="btn btn-contact d-md-none d-sm-block">Contact</a>
                                    </div>
                                </div>
                                <hr/>
                                <div class="row">
                                    <div class="col-sm-9 col-9">
                                        <p class="confirm">Confirming your Match</p>
                                        <p class="arrives">Arrives <span>@{{ arrivesIn }}</span></p>
                                    </div>
                                    <!--<div class="col-sm-3 col-3 track">
                                        <a href="#" @click.prevent="details()"><i class="fa fa-exclamation-circle"></i></a>
                                        <a href="#" @click.prevent="details()" class="tr_link">Track</a>
                                    </div>-->
                                </div>
                                <div class="row">
                                    <no-provider inline-template
                                                              find-new-action="{{ route('shifts.find-new', $shift) }}"
                                                              result-action="{{ route('shifts.result', $shift) }}"
                                    >
                                        <div class="col" v-if="showButton()">
                                            <a href="#" @click.prevent="noProviderModalShow()" class="btn gray_button">Provider Didn’t Show Up</a>
                                            @include("shift._no-provider-modal")
                                        </div>
                                    </no-provider>
                                    <div class="col" v-if="canCancel()">
                                        <button type="button" class="btn btn-danger btn-big" @click="cancel()">Cancel Shift</button>
                                    </div>
                                    @if(!$shift->isHasReviewFromPractice())
                                        @if ($shift->startsInHours() <= 0 && !$shift->isCompleted() && !$shift->isFinishedStatus())
                                            <div class="col">
                                                <form method="post" action="{{ route('shifts.finish', $shift) }}">
                                                    @csrf
                                                    <button type="submit" class="btn form-button" @click="$loading.show()">End Shift</button>
                                                </form>
                                            </div>
                                        @elseif ($shift->isCompleted())
                                            <div class="col">
                                                <a class="btn form-button" @click="$loading.show()" href="{{ route('shifts.reviews.review', $shift) }}">Leave a Review</a>
                                            </div>
                                        @endif
                                    @else
                                        <div class="col">
                                            <div class="alert alert-primary text-center" role="alert">
                                                You have already finished shift.
                                            </div>
                                        </div>
                                    @endif
                                    </div>
                                </div>
                                <div v-else class="track-div">
                                    <div class="row">
                                        <div class="col-1">
                                            <a @click.prevent="details()" class="back-chevron"><i class="fa fa-chevron-left"></i></a>
                                        </div>
                                        <div class="col-10">
                                            <p class="divtitle">Tracking</p>
                                        </div>
                                    </div>
                                    <hr/>
                                    <div class="row">
                                        <p class="track-li" :class="{'active' : getStatus == 1}"><i class="fa fa-eercast"></i> <span>Accepted Request</span></p>
                                        <p class="track-li" :class="{'active' : getStatus == 2}"><i class="fa fa-car"></i> <span>Your Provider is On The Way</span></p>
                                        <p class="track-li" :class="{'active' : getStatus == 3}"><i class="fa fa-briefcase"></i> <span>Shift in Progress</span></p>
                                        <p class="track-li" :class="{'active' : getStatus == 4}"><i class="last fa fa-home"></i> <span>Shift Completed</span></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @include("shift._reason-modal")
            </div>

    </shift-details>
@endsection
