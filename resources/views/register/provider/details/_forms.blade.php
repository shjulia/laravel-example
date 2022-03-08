<account-details
    inline-template
    show-holidays-init="{{ !!old('show-holidays', count($specialist->additional->holidays)) }}"
    photo-init="{{ $specialist->photo ? $specialist->photo_url  : null }}"
    :days="{{ collect($days) }}"
    :init-availabilities="{{ !empty($specialist->additional->availabilities)
                            ? collect($specialist->additional->availabilities)
                            : collect($specialist->parsedAvailabilities(old('day'), old('from'), old('to')))
                        }}"
    autocomplete-action="{{ route('details.autocomplete') }}"
    place-action="{{ route('details.placeData') }}"
    v-cloak
    provider_adress="{{ $specialist->driver_address ?? '' }}"
    provider_city="{{ $specialist->driver_city ?? '' }}"
    provider_state="{{ $specialist->driver_state ?? '' }}"
    provider_zip="{{  $specialist->driver_zip ?? ''}}"
    set-time-show-init="{{ !empty(old('from')) }}"
    info-set="{{ !!$specialist->user->wallet->has_transfer_data }}"
>
    <div>
        <div class="text-center d-cropper-div">
            <div class="img-div">
                <img v-if="userAvatar" :src="userAvatar">
                <i v-else class="fa fa-camera"></i>
            </div>
            <button id="pick-avatar" class="btn btn-success">@{{ userAvatar ? 'Change photo' : 'Select photo' }}</button>
            <avatar-cropper
                trigger="#pick-avatar"
                upload-url="{{ $uploadAvatarUrl }}"
                :labels="buttonsLabels"
                :output-options="outputOptions"
                :upload-headers="headers"
                :cropper-options="cropperOptions"
                @uploading="handleUploading"
                @uploaded="handleUploaded"
                @error="handlerError"
            ></avatar-cropper>
            <span class="invalid-feedback" role="alert" v-if="uploadError">
                <strong>@{{ uploadError }}</strong>
            </span>
            @if ($errors->has('photo'))
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $errors->first('photo') }}</strong>
                </span>
            @endif
        </div>

        <div class="text-center">
            <h4>{{ $specialist->position->title }}</h4>
            <h3>{{ $specialist->user->first_name . ' ' . $specialist->user->last_name }}</h3>
        </div>

        <form method="POST" ref="baseform" action="{{ $formAction }}" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <h4 class="detailsh4">Specialities</h4>
                <select
                    multiple="multiple"
                    name="specialities[]"
                    class="select2 form-control{{ $errors->has('specialities') ? ' is-invalid' : '' }}"
                    ref="select2">
                    @foreach($specialities as $speciality)
                        <option
                            value="{{ $speciality->id }}"
                            @if(in_array($speciality->id, old('specialities', $specialist->additional->specialities))) selected @endif
                        >{{ $speciality->title }}</option>
                    @endforeach
                </select>
                @if ($errors->has('specialities'))
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $errors->first('specialities') }}</strong>
                    </span>
                @endif
            </div>
            <div class="form-group">
                <h4 class="detailsh4">Routine tasks</h4>
                <select
                    multiple="multiple"
                    name="routine_tasks[]"
                    class="select2 form-control{{ $errors->has('routine_tasks') ? ' is-invalid' : '' }}"
                    ref="select2">
                    @foreach($routine_tasks as $routine_task)
                        <option
                            value="{{ $routine_task->id }}"
                            @if( in_array($routine_task->id, $specialist_routine_tasks) )
                            selected
                            @endif
                        >
                            {{ $routine_task->title }}
                        </option>
                    @endforeach
                </select>
                @if ($errors->has('routine_tasks'))
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $errors->first('routine_tasks') }}</strong>
                    </span>
                @endif
            </div>
            <h4 class="detailsh4">Availabilities</h4>
            <div class="row av-time-div">
                <div class="col-md-12">
                    <a @click.porevent="showSetTime()" class="set-time"><i class="fa fa-clock-o grey" aria-hidden="true"></i> Set your time</a>
                    <input type="hidden" name="delete" :value="setTimeShow ? 1 : 0">
                </div>
                <div class="col-md-12" style="padding:2px;">
                    <div v-if="setTimeShow">
                        <div class="card">
                            <div class="card-header text-center">
                                <span class="header-card">Set your time</span>
                            </div>
                            <div class="card-body">
                                <span @click="newInterval()" class="new-interval"><i class="fa fa-plus" aria-hidden="true"></i> Add Your Availability</span>
                                <div class="clearfix"></div>
                                <time-row
                                    v-for="(availability, index) in availabilities"
                                    v-if="!isRemoved(availability.id)"
                                    :key="availability.id"
                                    :from-init="availability.from"
                                    :to-init="availability.to"
                                    :in-days-init="availability.inDays"
                                    :id="availability.id"
                                    :iterator="index"
                                ></time-row>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <hr/>
            <div class="form-group row">
                <label class="col-9 col-sm-10 col-form-label holiday-label">Availability at holidays</label>
                <div class="col-3 col-sm-2 custom-control custom-switch">
                    <input type="checkbox" id="show-holidays" class="custom-control-input" value="1" name="show-holidays" @if(old('show-holidays', count($specialist->additional->holidays))) checked @endif v-model="showHolidays">
                    <label class="custom-control-label" for="show-holidays"> </label>
                </div>
            </div>

            <hr/>

            <div v-if="showHolidays" class="holidays-div">
                <h4 class="detailsh4">Availabilities</h4>
                @foreach($holidays as $holiday)
                    <div class="form-group row">
                        <label class="col-9 col-sm-10 col-form-label holiday-label">{{ $holiday->title }}</label>
                        <div class="col-3 col-sm-2 custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="holiday[{{$holiday->id}}]" name="holiday[{{$holiday->id}}]" value="1" @if(old('holiday.' . $holiday->id, $specialist->additional->holidays[$holiday->id] ?? false)) checked @endif>
                            <label class="custom-control-label" for="holiday[{{$holiday->id}}]"> </label>
                            @if ($errors->has('holiday.' . $holiday->id))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('holiday.' . $holiday->id) }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="bank-details">
                <h4>Bank details</h4>
                <div v-if="!infoSet || showBankFields">
                    <Cinput
                        label="Routing Number"
                        id="routing_number"
                        type="text"
                        name="routing_number"
                        mask="##########"
                        value="{{ old('routing_number', $funding_sources[0]->routing_number ?? '') }}"
                        has-errors="{{ $errors->has('routing_number') }}"
                        first-error="{{ $errors->first('routing_number') }}"
                        :is-mat="true"
                    ></Cinput>

                    <Cinput
                        label="Account Number"
                        id="account_number"
                        type="text"
                        name="account_number"
                        mask="################"
                        value="{{ old('account_number', $funding_sources[0]->account_number ?? '') }}"
                        has-errors="{{ $errors->has('account_number') }}"
                        first-error="{{ $errors->first('account_number') }}"
                        :is-mat="true"
                    ></Cinput>
                </div>
                <a v-if="infoSet" href="#" @click.prevent="showBankFields = !showBankFields" class="boon-link">Change data to get paid</a>
            </div>

            <h4 class="detailsh4">Location</h4>

            <Cinput
                label="Address"
                id="address"
                type="text"
                name="address"
                value="{{ old('address', $specialist->driver_address) }}"
                has-errors="{{ $errors->has('address') }}"
                first-error="{{ $errors->first('address') }}"
                :is-mat="true"
                :init-model="address"
                init-model-attr="address"
                @keyup.native="getAddress()"
            ></Cinput>

            <div class="autocomplete details-autocomplete" v-if="Object.keys(addresses).length && showAutocompleteList">
                <div v-for="(value, key) in addresses">
                    <a href="#"
                       @click.prevent="selectPlace(key, value)"
                       v-html="formatedName(value)"
                    ></a>
                    <hr/>
                </div>
            </div>

            <Cinput
                label="City"
                id="city"
                type="text"
                name="city"
                value="{{ old('city', $specialist->driver_city) }}"
                has-errors="{{ $errors->has('city') }}"
                first-error="{{ $errors->first('city') }}"
                :is-mat="true"
                :init-model="city"
                init-model-attr="city"
            ></Cinput>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group mat">
                        <label for="state">State</label>
                        <select
                            id="state"
                            ref="state"
                            class="select2 form-control{{ $errors->has('state') ? ' is-invalid' : '' }}"
                            name="state"
                            v-select2
                        >
                            @foreach ($states as $state)
                                <option value="{{ $state->short_title }}" @if($specialist->driver_state == $state->short_title) selected @endif>{{ $state->title }}</option>
                            @endforeach
                        </select>
                        @if ($errors->has('state'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('state') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>
                <div class="col-md-6">
                    <Cinput
                        label="Zip"
                        id="zip"
                        type="text"
                        name="zip"
                        value="{{ old('zip', $specialist->driver_zip) }}"
                        has-errors="{{ $errors->has('zip') }}"
                        first-error="{{ $errors->first('zip') }}"
                        :is-mat="true"
                        :number-input="true"
                        :init-model="zip"
                        init-model-attr="zip"
                        mask="#########"
                    ></Cinput>
                </div>
            </div>
            <div class="form-group">
                <button type="submit" class="btn form-button" @click="$loading.show()">Continue</button>
            </div>
        </form>

        <div class="text-center">
            @if($user->isAccountActive())
                <form method="post" action="{{ route('deactivate-account', $user) }}">
                    @csrf
                    <button type="submit" class="btn-link mt-2">Deactivate Account</button>
                </form>
            @else
                <form method="post" action="{{ route('activate-account', $user) }}">
                    @csrf
                    <button type="submit" class="btn-link mt-2">Activate Account</button>
                </form>
            @endif
        </div>
    </div>
</account-details>
