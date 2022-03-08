<register-step3
    autocomplete-action="{{ route('signup.autocomplete') }}"
    place-action="{{ route('signup.placeData') }}"
    inline-template
    showforminit="{{ $errors->any() || $user->specialist->driver_photo }}"
    action="{{ $action }}"
    next-action="{{ $nextAction }}"
    old-file-init="{{ $user->specialist->driver_photo ? $user->specialist->driver_photo_url : null }}"
    :user="{{ $user }}"
    phone-action="{{ $phoneAction }}"
    phone-error-init="{{ $errors->has('phone') }}"
    v-cloak
>
    <div class="row">
        <div class="col-md-12">
            <Cinput
                label="Mobile Phone"
                id="phone"
                type="tel"
                name="phone"
                :required="false"
                :is-mat="true"
                :prepend="true"
                prepend-icon="mobile big"
                mask="(###) ###-####"
                :number-input="true"
                autocomplete="off"
                :init-model="phone"
                init-model-attr="phone"
                :has-errors="showPhoneError"
                first-error="Phone must be set"
                @blur-input="phoneChanged()"
            ></Cinput>
        </div>
        <div class="col-md-12">
            <form ref="photoform">
                <div class="input-group">
                    <div class="custom-file">
                        <input ref="photo" type="file" name="photo" accept="image/*" class="custom-file-input" @click="fileInputClick($event)" @change="onChange()">
                        <label class="custom-file-label little-custom-file-label">Choose a photo of driver license</label>
                    </div>
                    <div class="input-group-append fileinput-button" v-if="oldFile">
                        <button @click="showFile()" class="btn btn-outline-primary" type="button">Show photo</button>
                    </div>
                </div>
                <div class="invalid-feedback" v-if="server_errors.photo">@{{ server_errors.photo }}</div>
            </form>
        </div>

        <div class="col-md-12" v-show="showForm">
            @if (isset($removeRoute))
                <form action="{{ $removeRoute }}" method="post">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn boon-link">Remove license</button>
                </form>
            @endif
            <br/>
            <form method="POST" action="{{ $formAction }}" ref="identityForm">
                @csrf

                <div class="row">
                    <div class="col-sm-6">
                        <Cinput
                            label="First Name"
                            id="first_name"
                            type="text"
                            name="first_name"
                            value="{{ old('first_name') }}"
                            has-errors="{{ $errors->has('first_name') }}"
                            first-error="{{ $errors->first('first_name') }}"
                            :required="false"
                            :is-mat="true"
                            :init-model="first_name"
                            init-model-attr="first_name"
                        ></Cinput>
                    </div>
                    <div class="col-sm-6">
                        <Cinput
                            label="Last Name"
                            id="last_name"
                            type="text"
                            name="last_name"
                            value="{{ old('last_name') }}"
                            has-errors="{{ $errors->has('last_name') }}"
                            first-error="{{ $errors->first('last_name') }}"
                            :required="false"
                            :is-mat="true"
                            :init-model="last_name"
                            init-model-attr="last_name"
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
                            value="{{ old('middle_name') }}"
                            has-errors="{{ $errors->has('middle_name') }}"
                            first-error="{{ $errors->first('middle_name') }}"
                            :required="false"
                            :is-mat="true"
                            :init-model="middle_name"
                            init-model-attr="middle_name"
                        ></Cinput>
                    </div>
                    <div class="col-sm-12" v-if="!isNamesTheSame()">
                                        <span class="valid-feedback" style="margin-bottom: 10px; display: block; margin-top:-5px" role="alert">
                                            <strong>Name that was recognised from your driver license doesn't match one you mentioned on Step 1</strong>
                                        </span>
                    </div>
                </div>

                <Cinput
                    label="Address"
                    id="address"
                    type="text"
                    name="address"
                    value="{{ old('address') }}"
                    has-errors="{{ $errors->has('address') }}"
                    first-error="{{ $errors->first('address') }}"
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

                <Cinput
                    label="City"
                    id="city"
                    type="text"
                    name="city"
                    value="{{ old('city') }}"
                    has-errors="{{ $errors->has('city') }}"
                    first-error="{{ $errors->first('city') }}"
                    :required="false"
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
                                v-model="state"
                                class="form-control{{ $errors->has('state') ? ' is-invalid' : '' }}"
                                name="state"
                                ref="select2_state"
                                v-select2="state"
                            >
                                @foreach ($states as $state)
                                    <option value="{{ $state->short_title }}">{{ $state->title }}</option>
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
                            value="{{ old('zip') }}"
                            has-errors="{{ $errors->has('zip') }}"
                            first-error="{{ $errors->first('zip') }}"
                            :required="false"
                            :is-mat="true"
                            :init-model="zip"
                            :number-input="true"
                            init-model-attr="zip"
                            mask="#########"
                        ></Cinput>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group mat">
                            <label for="gender">Gender</label>
                            <select
                                id="gender"
                                class="form-control{{ $errors->has('gender') ? ' is-invalid' : '' }}"
                                name="gender"
                                v-model="gender"
                                ref="select2_gender"
                                v-select2
                            >
                                <option value="M">Male</option>
                                <option value="F">Female</option>
                            </select>
                            @if ($errors->has('gender'))
                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $errors->first('gender') }}</strong>
                                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <Cinput
                            label="Date of Birth"
                            id="dob"
                            type="date"
                            name="dob"
                            value="{{ old('dob') }}"
                            has-errors="{{ $errors->has('dob') }}"
                            first-error="{{ $errors->first('dob') }}"
                            :required="true"
                            :is-mat="true"
                            :init-model="dob"
                            init-model-attr="dob"
                        ></Cinput>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-6">
                        <Cinput
                            label="Driver license number"
                            id="license"
                            type="text"
                            name="license"
                            value="{{ old('license') }}"
                            has-errors="{{ $errors->has('license') }}"
                            first-error="{{ $errors->first('license') }}"
                            :required="false"
                            :is-mat="true"
                            :init-model="license"
                            init-model-attr="license"
                        ></Cinput>
                    </div>
                    <div class="col-sm-6">
                        <Cinput
                            label="Expiration Date"
                            id="expiration_date"
                            type="date"
                            name="expiration_date"
                            value="{{ old('expiration_date') }}"
                            has-errors="{{ $errors->has('expiration_date') }}"
                            first-error="{{ $errors->first('expiration_date') }}"
                            :required="true"
                            :is-mat="true"
                            :init-model="expiration_date"
                            init-model-attr="expiration_date"
                        ></Cinput>
                    </div>
                </div>

                <div class="form-group">
                    <button @click.prevent.stop="submit()" type="submit" class="btn form-button">Continue</button>
                </div>
            </form>
        </div>
    </div>
</register-step3>
