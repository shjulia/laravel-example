@extends('layouts.main')

@section('content')
    <provider-index
        inline-template
        v-cloak
    >
        <div class="hire">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-md-12">
                        <div class="card card-null">
                            <div class="card-body">
                                <div class="div-big-button">
                                    <img src="{{ asset('img/pr_hands.png') }}" alt="">
                                    <a href="{{ route('shifts.base', ['shift' => null, 'now' => true]) }}" @click="$loading.show()" class="request-provider">
                                        Request Provider
                                    </a>
                                </div>
                                <div class="text-right">
                                    <a class="little-button" href="{{ route('shifts.base', ['now' => false]) }}" @click="$loading.show()">
                                        Request Provider
                                        <span class="plus-icon">+</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="container-fluid upcoming" :class="{'abs' : abs}">
                <div class="container">
                    <div class="row">
                        <div class="col-10">
                            <ul class="nav nav-tabs" id="shiftsList" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" id="upcoming-tab" data-toggle="tab" href="#upcoming" role="tab" aria-controls="upcoming" aria-selected="true"><p class="title mb-0">Upcoming Providers</p></a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="progress-tab" data-toggle="tab" href="#progress" role="tab" aria-controls="progress" aria-selected="false"><p class="title mb-0">Shifts in progress</p></a>
                                </li>
                            </ul>

                            <div class="tab-content">
                                <div class="tab-pane active" id="upcoming" role="tabpanel" aria-labelledby="upcoming-tab">
                                    <div class="tabwrap">
                                        <p></p>
                                        @foreach($shifts as $shift)
                                            @include('shift._one-shift-row', ['shift' => $shift])
                                        @endforeach
                                        @if($shifts->isEmpty())
                                            <div class="alert alert-primary" role="alert">
                                                There are no upcoming shifts
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <div class="tab-pane" id="progress" role="tabpanel" aria-labelledby="progress-tab">
                                    <div class="tabwrap">
                                        <p></p>
                                        @foreach($inProgress as $shift)
                                            @include('shift._one-shift-row', ['shift' => $shift])
                                        @endforeach
                                        @if($inProgress->isEmpty())
                                            <div class="alert alert-primary" role="alert">
                                                There are no shifts in progress
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-2">
                            <p class="gotop">
                                <i class="fa go-abs-prov" :class="{'fa-chevron-up' : !abs, 'fa-chevron-down' : abs}" @click="goAbs()"></i>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </provider-index>

@endsection
