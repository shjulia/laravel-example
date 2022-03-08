@extends('layouts.onboarding', ['h2' => 'How far are you willing to travel for a shift?'])
@section('content')
    <max-distance
        inline-template
        :distance-init="{{ $user->specialist->shift_distance_max ?: 60 }}"
        :duration-init="{{ $user->specialist->shift_duration_max ?: 45 }}"
        init-type="{{ $user->specialist->shift_distance_max ? ($user->specialist->shift_distance_max != 25 ? 'distance' : '') : 'duration' }}"
        v-cloak
    >
        <form method="POST" action="{{ route('provider.onboarding.distance') }}">
            @csrf
            <div class="onboarding-cont">
                <div class="row">
                    <div class="col-6 col-sm-6">
                        <div class="distance-type" :class="{'active' : isDuration()}" @click="selectType('duration')">
                            <div class="custom-control custom-checkbox">
                                <input class="form-check-input custom-control-input" type="checkbox" id="duration-c" name="is_duration" value="1" v-model="durationCheckbox" @change="selectType('duration')">
                                <label for="duration-c" class="custom-control-label"></label>
                            </div>
                            <div class="cont">
                                <i class="fa fa-clock-o"></i>
                                <p>Time</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-sm-6">
                        <div class="distance-type" :class="{'active' : isDistance()}" @click="selectType('distance')">
                            <div class="custom-control custom-checkbox">
                                <input class="form-check-input custom-control-input" type="checkbox" id="distance-c" value="1" name="is_distance" v-model="distanceCheckbox" @change="selectType('distance')">
                                <label for="distance-c" class="custom-control-label"></label>
                            </div>
                            <div class="cont">
                                <i class="fa fa-car"></i>
                                <p>Distance</p>
                            </div>
                        </div>
                    </div>
                </div>
                @if ($errors->has('is_duration'))
                    <span class="invalid-feedback" role="alert">
                    <strong>{{ $errors->first('is_duration') }}</strong>
                </span>
                @endif
                @if ($errors->has('is_distance'))
                    <span class="invalid-feedback" role="alert">
                    <strong>{{ $errors->first('is_distance') }}</strong>
                </span>
                @endif
                <div class="row range-input wb" v-if="isDuration()">
                    <div class="col-12 mt-5">
                        <vue-slider v-model="duration" :height="20" :dot-size="20" :min="5" :tooltip="'always'" :max="120" :marks="durationMarks"></vue-slider>
                        <input type="hidden" name="duration" :value="duration" />
                    </div>
                    <div class="col-6">
                        <span class="mark-title">5</span>
                        <br/>
                        <span class="mark-desc">minutes</span>
                        <br/>
                        <span class="mark-desc">minimum</span>
                    </div>
                    <div class="col-6 text-right">
                        <span class="mark-title">120</span>
                        <br/>
                        <span class="mark-desc">minutes</span>
                        <br/>
                        <span class="mark-desc">maximum</span>
                    </div>
                </div>
                <div class="row range-input wb" v-if="isDistance()">
                    <div class="col-12 mt-5">
                        <vue-slider v-model="distance" :height="20" :dot-size="20" :min="4" :tooltip="'always'" :max="150" :marks="distanceMarks"></vue-slider>
                        <input type="hidden" name="distance" :value="distance" />
                    </div>
                    <div class="col-6">
                        <span class="mark-title">4</span>
                        <br/>
                        <span class="mark-desc">miles</span>
                        <br/>
                        <span class="mark-desc">minimum</span>
                    </div>
                    <div class="col-6 text-right">
                        <span class="mark-title">150</span>
                        <br/>
                        <span class="mark-desc">miles</span>
                        <br/>
                        <span class="mark-desc">maximum</span>
                    </div>
                </div>
                @if ($errors->has('distance'))
                    <span class="invalid-feedback" role="alert">
                    <strong>{{ $errors->first('distance') }}</strong>
                </span>
                @endif
                @if ($errors->has('duration'))
                    <span class="invalid-feedback" role="alert">
                    <strong>{{ $errors->first('duration') }}</strong>
                </span>
                @endif
            </div>

            <div class="text-center continue-butt">
                <button type="submit" class="btn btn-bg-grad">Continue</button>
                <br/>
                <a href="{{ route('provider.onboarding.rate') }}" class="later">Add Later</a>
            </div>
            @include('register.provider.onboarding._progress', ['percent' => 43])
        </form>
    </max-distance>
@endsection
