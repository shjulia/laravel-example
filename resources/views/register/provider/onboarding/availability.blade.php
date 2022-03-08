@extends('layouts.onboarding', ['h2' => "When are you available?"])
@section('content')
    <account-details
        inline-template
        :days="{{ collect($days) }}"
        :init-availabilities="{{ !empty($specialist->additional->availabilities)
                            ? collect($specialist->additional->availabilities)
                            : collect($specialist->parsedAvailabilities(old('day'), old('from'), old('to')))
                        }}"
        set-time-show-init="{{ true }}"
        v-cloak
    >
        <div>
            <form action="{{ route('provider.onboarding.availability') }}" method="POST">
                @csrf
                <div class="onboarding-cont">
                    <div>
                        <div class="row av-time-div">
                            <div class="col-md-12">
                                <input type="hidden" name="delete" :value="setTimeShow ? 1 : 0">
                            </div>
                            <div class="col-md-12" style="padding:2px;">
                                <div v-if="setTimeShow">
                                    <div class="card text-white">
                                        <div class="card-body">
                                            <div class="time-rows-div">
                                                <onboarding-time-row
                                                    v-for="(availability, index) in availabilities"
                                                    v-if="!isRemoved(availability.id)"
                                                    :key="availability.id"
                                                    :from-init="availability.from"
                                                    :to-init="availability.to"
                                                    :in-days-init="availability.inDays"
                                                    :id="availability.id"
                                                    :iterator="index"
                                                ></onboarding-time-row>
                                            </div>
                                            <p @click="newInterval()" class="new-interval"><i class="fa fa-plus" aria-hidden="true"></i> Add Your Availability</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="text-center continue-butt">
                    <button
                        type="submit"
                        class="btn btn-bg-grad"
                        v-if="Object.keys(availabilities).length > 0"
                    >Continue</button>
                    <br/>
                    <a href="{{ route('provider.onboarding.holidays') }}" class="later">Add Later</a>
                </div>
                @include('register.provider.onboarding._progress', ['percent' => 86])
            </form>
        </div>

    </account-details>
@endsection
