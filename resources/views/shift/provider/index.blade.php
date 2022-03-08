@extends('layouts.main')

@section('content')

    <provider-dashboard
        inline-template
        :items="{{ collect( $shifts ? $shifts->items() : [] ) }}"
        change-availability-link="{{ route('shifts.provider.available') }}"
        :state="{{ auth()->user()->specialist->available }}"
        v-cloak
        public-path="{{ public_path() }}"
        form-action="{{ route('shifts.provider.reviews.createReview') }}"
        start-url="{{ isset($item) ? route('shifts.provider.start', $item) : '' }}"
        finish-url="{{ isset($item) ? route('shifts.provider.finish', $item) : '' }}"
        :review="{{ $review ?? 0 }}"
        :shift-id="{{ $shift->id ?? 0 }}"
        :scores="{{ $scores }}"
        :shift="{{ $shift ?? 'null' }}"
    >
        <div class="shift-provider">
            <section class="availability row">
                <div class="left-block">
                    <div class="update-availability">
                        <h5 class="title">
                            @{{ available ? 'Available for hire' : 'Not available for hire' }}
                        </h5>
                        <a href="{{ route('account-details') }}">
                            Update Availability
                        </a>
                    </div>
                </div>
                <div
                    class="available-button right-block"
                    :class="[available ? 'on': 'off']"
                >
                    <label for="available">

                    </label>
                    <input type="checkbox"
                           id="available"
                           @change="hire"
                           v-model="available"
                    >
                </div>
            </section>

            <section class="map">
                @if (empty($shifts) && !isset($review))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        It looks like you have no scheduled jobs. Make sure your availability is up to date.
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif
                <job-map
                    :items="items"
                    date="{{ date('Y-m-d') }}"
                    action="{{ route('shifts.provider.resultShow', ['shift' => '_']) }}"
                    accept-action="{{ route('shifts.provider.acceptPage', ['shift' => '_']) }}"
                    details-url="{{ route('shifts.provider.info', ['shift' => '_']) }}"
                    :specialist="{{ $provider }}"
                    :item="{{ (isset($item) && $item) ? $item : 'null' }}"
                    ref="map"
                    v-if="available"
                ></job-map>

                <div
                    v-else
                    class="go-online"
                >
                    <div class="message">
                        <h3>GO ONLINE <i class="fa fa-arrow-up"></i></h3>
                    </div>
                </div>

                <shift-calendar
                    :items="{{ collect( $shifts ? $shifts->items() : [] ) }}"
                    inline-template
                    v-cloak
                    v-show="showCalendar"
                    withdraw-url="{{ route('shifts.provider.withdraw') }}"
                    balance="{{ $provider->user->wallet->balance ?? 0  }}"
                >
                    <div class="shift-data">
                        <div class="calendar row">
                            <div class="w-50">
                                <h5>See Upcoming Shift</h5>
                            </div>
                            <div class="w-50 text-right">
                                <img src="{{ asset('/img/calendar.png') }}" alt="calendar">
                            </div>
                            <div class="week-day-name">
                                @for($i=0; $i <=6; $i++)
                                    <span>{{ now()->addDay($i)->format('D') }}</span>
                                @endfor
                            </div>
                            <div class="days">
                                <div class="day" v-for="date in days">
                                    <a href="#"

                                       @click.prevent="selectDay(date.timestamp)"
                                       :class="{active: date.active, marker: date.hasShift}"
                                    >@{{ date.day }}</a>
                                </div>
                            </div>
                            <div class="w-50 selected-day">
                                <h5>@{{ selectedDay }}</h5>
                            </div>
                            <div class="w-50 text-right selected-day">
                                <a href="#" @click.prevent="selectDay(null)">
                                    <h5>See All</h5>
                                </a>
                            </div>
                        </div>
                        <div class="earnings">
                            <div>
                                <h4>$@{{ money }}</h4>
                                <span>Earnings</span>
                            </div>
                            <div>
                                <button @click="withdraw()">Withdraw</button>
                                <p class="mb-1"></p>
                                <a class="boon-link block" href="{{ route('provider.edit.getPaid') }}">Change bank details</a>
                            </div>
                        </div>
                    </div>
                </shift-calendar>

                <div class="abs-white details-div container" v-if="selectedShift && (showDetails || showTracking)">
                    <div v-show="showDetails">
                        <div class="row">
                            <div class="col-2">
                                <a href="{{ route('shifts.provider.index') }}" @click="$loading.show()" class="back-chevron black">
                                    <i class="fa fa-chevron-left"></i>
                                </a>
                            </div>
                            <div class="col-md-2 col-2">
                                <img class="circle-ava" :src="selectedShift.practice.practice_photo_url"/>
                            </div>
                            <div class="col-md-7 col-5">
                                <p class="title">@{{ selectedShift.practice_location.practiceName }}</p>
                                <p class="desc">@{{ status() }}</p>
                            </div>
                            <div class="col-md-3 col-3 text-right">
                                <a :href="'tel:' + selectedShift.practice_location.practicePhone" v-if="selectedShift.practice_location.practicePhone" class="btn btn-contact only-mobile p-0">Contact</a>
                            </div>
                        </div>
                        <hr/>
                        <div class="row">
                            <div class="col-12">
                                <p class="arrives mb-1">@{{ shiftAddress }} <span>@{{ shiftTime }}</span></p>
                                <p class="arrives" v-if="!isArrived">Arrives in <span>@{{ arrivesIn }}</span></p>
                                <p class="arrives" v-else>Status <span>@{{ status() }}</span></p>
                            </div>
                            {{--<div class="col-2 track">
                                <a href="#" @click.prevent="showTrackingBlock()"><i class="fa fa-exclamation-circle"></i></a>
                                <a href="#" @click.prevent="showTrackingBlock()" class="tr_link">Track</a>
                            </div>--}}
                        </div>
                        <div class="row">
                            <div class="col-12">
                                @if (isset($item))
                                    @can('start-shift', $item)
                                        <button @click="arrived()" type="submit" class="btn form-button">I've Arrived, Start Shift</button>
                                    @endcan
                                    @if(!$item->isHasReviewFromProvider())
                                        @can ('finish-shift', $item)
                                            <div class="col">
                                                <button class="btn form-button" @click="finishClick()">I’m Finished - End Shift</button>
                                            </div>
                                        @endcan
                                        @if($item->isCompleted())
                                            <div class="col">
                                                <a class="btn form-button" @click="$loading.show()" href="{{ route('shifts.provider.reviews.review', $item) }}">Leave a Review</a>
                                            </div>
                                        @endif
                                    @else
                                        <div class="col">
                                            <div class="alert alert-primary text-center" role="alert">
                                                You have already finished shift.
                                            </div>
                                        </div>
                                    @endif
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="track-div" v-show="showTracking">
                        <div class="row">
                            <div class="col-1">
                                <a @click.prevent="showDetailsBlock" class="back-chevron">
                                    <i class="fa fa-chevron-left"></i>
                                </a>
                            </div>
                            <div class="col-10">
                                <p class="divtitle">Tracking</p>
                            </div>
                        </div>
                        <hr/>
                        <div class="row">
                            <p class="track-li" :class="{'active' : getStatus() == 1}">
                                <i class="fa fa-eercast"></i> <span>Accepted Request</span>
                            </p>
                            <p class="track-li" :class="{'active' : getStatus() == 2}">
                                <i class="fa fa-car"></i> <span>Your Provider is On The Way</span>
                            </p>
                            <p class="track-li" :class="{'active' : getStatus() == 3}">
                                <i class="fa fa-briefcase"></i> <span>Shift in Progress</span>
                            </p>
                            <p class="track-li" :class="{'active' : getStatus() == 4}">
                                <i class="last fa fa-home"></i> <span>Shift Completed</span>
                            </p>
                        </div>
                    </div>
                </div>
            </section>

            @include('shift.provider._review')
        </div>

    </provider-dashboard>
@endsection
