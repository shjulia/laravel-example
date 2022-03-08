@extends('layouts.auth')

@section('content')
    <div class="wraper wraper-login">
        <div class="auth-container">
            <div class="auth-form step-user-base">
                @include('partials._form-titles', [
                    'h1' => 'Background Check Stage',
                    'desc' => 'We take security extremely seriously, for both your information and the safety of patients. Your social security number is immediately encrypted and is used only for secure payment and background check information.',
                    'backUrl' => route('signup.license', ['code' => $user->tmp_token])
                ])

                @include("register.provider._stepper", [
                    'active' => 'check'
                ])
                <chekr-step
                    :specialist="{{ $user->specialist}}"
                    :user="{{ $user}}"
                    autocomplete-action="{{ route('signup.autocomplete') }}"
                    place-action="{{ route('signup.placeData') }}"
                    edit-url="{{ route('signup.identityEdit', ['code' => $user->tmp_token]) }}"
                    accept-init="{{ old('accept') }}"
                    accept-text-url="{{ route('fcra') }}"
                    inline-template
                >
                    <div>
                        <form method="POST" action="{{ route('signup.checkSave', ['code' => $user->tmp_token]) }}" v-cloak>
                            @csrf
                            <div>
                                <b>Name: </b><span v-html="getName"></span>
                                <br/>
                                <b>Address: </b>@{{ getFullAddress }}<br/>
                                <b>Phone: </b><span>@{{ phone_start }}</span><br/>
                                <b>Date of birth: </b><span>@{{ dob_start }}</span><br/>
                                @if($errors->has('custom_errors'))
                                    <div class="invalid-feedback">{{ $errors->first('custom_errors') }}</div>
                                @endif
                                <p class="text-right">
                                    <a href="#" @click.prevent="" data-toggle="modal" data-target="#editModal"><i class="fa fa-pencil-square-o"></i> Edit</a>
                                </p>
                            </div>

                            <Cinput
                                label="Social Security Number"
                                id="ssn"
                                type="tel"
                                name="ssn"
                                value="{{ old('ssn', $user->specialist->ssnVal) }}"
                                has-errors="{{ $errors->has('ssn') }}"
                                first-error="{{ $errors->first('ssn') }}"
                                mask="###-##-####"
                                :number-input="true"
                                :required="false"
                                :is-mat="true"
                            ></Cinput>

                            <Cinput
                                label="Confirm Social Security Number"
                                id="ssn_confirm"
                                type="tel"
                                name="ssn_confirm"
                                value="{{ old('ssn', $user->specialist->ssnVal) }}"
                                has-errors="{{ $errors->has('ssn_confirm') }}"
                                first-error="{{ $errors->first('ssn_confirm') }}"
                                mask="###-##-####"
                                :number-input="true"
                                :required="false"
                                :is-mat="true"
                            ></Cinput>

                            <div class="form-group">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="form-check-input custom-control-input" v-model="accept" id="accept" name="accept">
                                    <label class="custom-control-label" for="accept">Agree to <a href="javascript:void(0)" class="boon-link" @click.prevent="openAgreementModal()">Summary of Your Rights</a></label>
                                </div>
                                {{--<a href="javascript:void(0)" class="btn terms-button" @click="openAgreementModal()">
                                    <i v-if="accept" class="fa fa-check-circle-o" aria-hidden="true"></i>
                                    Agree to Background Check Conditions
                                </a>--}}
                                <span class="invalid-feedback" role="alert" v-if="showAcceptError">
                                <strong><i class="fa fa-exclamation-circle"></i>You must read and agree to terms to proceed.</strong>
                            </span>
                            </div>

                            <div class="form-group">
                                <button type="submit" class="btn form-button" @click="submit($event)">Continue</button>
                            </div>
                        </form>
                        <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="editModalLabel">Edit Name and Address</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="row">
                                            <div class="col-sm-6">
                                                <Cinput
                                                    label="First Name"
                                                    id="first_name"
                                                    type="text"
                                                    name="first_name"
                                                    :is-mat="true"
                                                    :init-model="first_name"
                                                    init-model-attr="first_name"
                                                    :has-errors="server_errors.first_name"
                                                    :first-error="server_errors.first_name"
                                                ></Cinput>
                                            </div>
                                            <div class="col-sm-6">
                                                <Cinput
                                                    label="Last Name"
                                                    id="last_name"
                                                    type="text"
                                                    name="last_name"
                                                    :is-mat="true"
                                                    :init-model="last_name"
                                                    init-model-attr="last_name"
                                                    :has-errors="server_errors.last_name"
                                                    :first-error="server_errors.last_name"
                                                ></Cinput>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" class="form-check-input custom-control-input" v-model="has_middle_name" id="has_middle_name" name="has_middle_name">
                                                    <label class="custom-control-label" for="has_middle_name">I do not have a middle name.</label>
                                                </div>
                                            </div>
                                            <div class="col-sm-6" v-if="!has_middle_name">
                                                <Cinput
                                                    label="Middle Name"
                                                    id="middle_name"
                                                    type="text"
                                                    name="middle_name"
                                                    :is-mat="true"
                                                    :init-model="middle_name"
                                                    init-model-attr="middle_name"
                                                    :has-errors="server_errors.middle_name"
                                                    :first-error="server_errors.middle_name"
                                                ></Cinput>
                                            </div>
                                        </div>
                                        <hr/>

                                        <div class="row">
                                            <div class="col-md-12">
                                                <Cinput
                                                    label="Address"
                                                    id="address"
                                                    type="text"
                                                    name="address"
                                                    :has-errors="server_errors.address"
                                                    :first-error="server_errors.address"
                                                    :required="false"
                                                    :is-mat="true"
                                                    :init-model="address"
                                                    init-model-attr="address"
                                                    @keyup.native="getAddress()"
                                                ></Cinput>

                                                <div class="autocomplete" v-if="Object.keys(addresses).length && showAutocompleteList">
                                                    <div v-for="(value, key) in addresses">
                                                        <a href="#"
                                                           @click.prevent="selectPlace(key, value)"
                                                           v-html="formatedName(value)"
                                                        ></a>
                                                        <hr/>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <Cinput
                                                    label="City"
                                                    id="city"
                                                    type="text"
                                                    name="city"
                                                    :required="false"
                                                    :is-mat="true"
                                                    :init-model="city"
                                                    init-model-attr="city"
                                                    :has-errors="server_errors.city"
                                                    :first-error="server_errors.city"
                                                ></Cinput>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group mat">
                                                    <label for="state">State</label>
                                                    <select
                                                        id="state"
                                                        v-model="state"
                                                        class="form-control"
                                                        :class="server_errors.state ? 'is-invalid' : ''"
                                                        name="state"
                                                        ref="select2_state"
                                                        v-select2="state"
                                                    >
                                                        @foreach ($states as $state)
                                                            <option value="{{ $state->short_title }}">{{ $state->title }}</option>
                                                        @endforeach
                                                    </select>
                                                    <div class="invalid-feedback" v-if="server_errors.state">@{{ server_errors.state }}</div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <Cinput
                                                    label="Zip"
                                                    id="zip"
                                                    type="text"
                                                    name="zip"
                                                    :required="false"
                                                    :is-mat="true"
                                                    :init-model="zip"
                                                    :number-input="true"
                                                    init-model-attr="zip"
                                                    mask="#########"
                                                    :has-errors="server_errors.zip"
                                                    :first-error="server_errors.zip"
                                                ></Cinput>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <Cinput
                                                    label="Mobile Phone"
                                                    id="phone"
                                                    type="tel"
                                                    name="phone"
                                                    :required="false"
                                                    :is-mat="true"
                                                    mask="(###) ###-####"
                                                    :number-input="true"
                                                    autocomplete="off"
                                                    :init-model="phone"
                                                    init-model-attr="phone"
                                                    :has-errors="server_errors.phone"
                                                    :first-error="server_errors.phone"
                                                ></Cinput>
                                            </div>
                                            <div class="col-md-6">
                                                <Cinput
                                                    label="Date of Birth"
                                                    id="dob"
                                                    type="date"
                                                    name="dob"
                                                    :has-errors="server_errors.dob"
                                                    :first-error="server_errors.dob"
                                                    :required="true"
                                                    :is-mat="true"
                                                    :init-model="dob"
                                                    init-model-attr="dob"
                                                ></Cinput>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <button type="button" class="btn form-button" @click.stop="submitEdits()">Continue</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </chekr-step>
            </div>
        </div>
    </div>
@endsection
