@extends('layouts.main')

@section('content')
        <shiftresult
                inline-template
                v-cloak
                :shift="{{ $shift }}"
                cancel-action="{{ route('shifts.cancel', $shift) }}"
                check-action="{{ route('shifts.checkChanges', $shift) }}"
                index-url="{{ route('shifts.index') }}"
        >
            <div>
                <div class="hire hire-center shift-result-hire">
                    <div class="centralform long">
                        <search-provider-map
                                :shift="shift"
                                :provider="provider"
                                :is-result="isResult"
                                :no-result="noResult"
                                change-payment-url="{{ route('practice.details.billing') }}"
                                apply-coupon-action="{{ route('shifts.coupon', $shift) }}"
                                v-cloak
                        ></search-provider-map>
                    </div>
                    <div class="modal fade transModal" id="acceptModal" tabindex="-1" role="dialog" aria-labelledby="acceptModalLabel" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content" v-if="isResult">
                                <div class="modal-header">
                                    <h5 class="modal-title m-auto" id="acceptModalLabel">You've been matched</h5>
                                    <p>@{{ provider.user.first_name }} on the way!</p>
                                </div>
                                <div class="modal-body">
                                    <div class="">
                                        <p class="provider-ava text-center" >
                                            @{{ provider.photo_url }}
                                            <img :src="provider.photo ? provider_photo : '/img/anonim.jpg'">
                                        </p>
                                        <div class="inputs">
                                            <div>
                                                <p class="name">@{{ provider.user.first_name + ' ' + provider.user.last_name }}</p>
                                                <p class="position">
                                                    <span class="pos">@{{ provider.position.title }}</span>
                                                    <span class="spec" v-for="task in provider.specialities">@{{ task.title }}</span>
                                                </p>
                                                <div class="row only-mobile" v-if="provider.phone">
                                                    <div class="col-sm-6 col-6">
                                                        <a :href="'sms:' + provider.phone" class="btn message"><i class="fa fa-comment" aria-hidden="true"></i> Message</a>
                                                    </div>
                                                    <div class="col-sm-6 col-6">
                                                        <a :href="'tel:' + provider.phone" class="btn call"><i class="fa fa-phone" aria-hidden="true"></i> Call</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <a href="{{ route('shifts.details', $shift) }}" @click="$loading.show()" class="btn form-button">Continue</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    @include("shift._reason-modal")
                </div>
            </div>

        </shiftresult>
@endsection
