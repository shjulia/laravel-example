@extends('layouts.onboarding', ['h2' => "Get Paid"])
@section('content')
    <get-paid
        inline-template
        init-type="{{ $user->specialist->payment_regime_status }}"
        info-set="{{ !!$user->wallet->has_transfer_data }}"
    >
        <form method="POST" action="{{ route('provider.edit.getPaid') }}">
            @csrf
            <div class="onboarding-cont bankd">
                <div class="row" v-if="!infoSet || showBankFields">
                    <div class="col-12">
                        <Cinput
                            label="Routing Number"
                            id="routing_number"
                            type="text"
                            name="routing_number"
                            mask="##########"
                            value="{{ old('routing_number') }}"
                            has-errors="{{ $errors->has('routing_number') }}"
                            first-error="{{ $errors->first('routing_number') }}"
                            :is-mat="true"
                        ></Cinput>
                    </div>
                    <div class="col-12">
                        <Cinput
                            label="Account Number"
                            id="account_number"
                            type="text"
                            name="account_number"
                            mask="################"
                            value="{{ old('account_number') }}"
                            has-errors="{{ $errors->has('account_number') }}"
                            first-error="{{ $errors->first('account_number') }}"
                            :is-mat="true"
                        ></Cinput>
                    </div>
                </div>
                <div class="row" v-if="infoSet">
                    <div class="col-md-12">
                        <a href="#" @click.prevent="showBankFields = !showBankFields" class="boon-link">Change data to get paid</a>
                    </div>
                </div>
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="distance-type paid-type" :class="{'active' : isStandard()}" @click="selectType('standard')">
                            <div class="custom-control custom-checkbox">
                                <input class="form-check-input custom-control-input" type="checkbox" id="standard-c" name="is_standard" value="1" v-model="standardCheckbox" @change="selectType('standard')">
                                <label for="duration-c" class="custom-control-label"></label>
                            </div>
                            <div class="cont">
                                <p class="title">Standard</p>
                                <p>Pay every week on Friday via ACH for no charge</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="distance-type paid-type mt-2" :class="{'active' : isExpedited()}" @click="selectType('expedited')">
                            <div class="custom-control custom-checkbox">
                                <input class="form-check-input custom-control-input" type="checkbox" id="expedited-c" value="1" name="is_expedited" v-model="expeditedCheckbox" @change="selectType('expedited')">
                                <label for="expedited-c" class="custom-control-label"></label>
                            </div>
                            <div class="cont">
                                <p class="title">Expedited</p>
                                <p>Pay after the shift has been worked via ACH for a 2.5% fee</p>
                            </div>
                        </div>
                    </div>
                </div>
                @if ($errors->has('is_standard'))
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $errors->first('is_standard') }}</strong>
                    </span>
                @endif
                @if ($errors->has('is_expedited'))
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $errors->first('is_expedited') }}</strong>
                    </span>
                @endif
            </div>
            <div class="text-center continue-butt">
                <button type="submit" class="btn btn-bg-grad" @click="$loading.show()">Save</button>
                <br/>
                <a href="{{ route('home') }}" class="later">Add Later</a>
            </div>
        </form>
    </get-paid>

@endsection
