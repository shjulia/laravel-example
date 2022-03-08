<practice-info
    autocomplete-action="{{ route('practice.signup.autocomplete') }}"
    place-action="{{ route('practice.signup.placeData') }}"
    name-init="{{ old('name', $user->practice->practice_name) }}"
    address-init="{{ old('address', $user->practice->address) }}"
    state-init="{{ old('state', $user->practice->state) }}"
    zip-init="{{ old('zip', $user->practice->zip) }}"
    url-init="{{ old('url', $user->practice->url) }}"
    phone-init="{{ old('phone', $user->practice->practice_phone) }}"
    :user="{{ $user }}"
    inline-template
    v-cloak
>
    <form method="POST" action="{{ $action }}" autocomplete="off">
        @csrf

        <Cinput
            label="Practice Name"
            id="name"
            type="text"
            name="name"
            :init-model="name"
            init-model-attr="name"
            value="{{ old('name', $user->practice->practice_name) }}"
            has-errors="{{ $errors->has('name') }}"
            first-error="{{ $errors->first('name') }}"
            :required="false"
            :is-mat="true"
            autocomplete="off"
            @blur-input="blurName()"
        ></Cinput>
        <div class="autocomplete" v-if="Object.keys(names).length">
            <div v-for="(value, key) in names">
                <a
                    href="#"
                    @click.prevent="selectPlace(key, value)"
                    v-html="formatedName(value)"
                ></a>
                <hr/>
            </div>
        </div>

        <Cinput
            label="Address"
            id="address"
            type="text"
            name="address"
            :init-model="address"
            init-model-attr="address"
            value="{{ old('address', $user->practice->address) }}"
            has-errors="{{ $errors->has('address') }}"
            first-error="{{ $errors->first('address') }}"
            :required="false"
            :is-mat="true"
        ></Cinput>

        <Cinput
            label="City"
            id="city"
            type="text"
            name="city"
            :init-model="city"
            init-model-attr="city"
            value="{{ old('city', $user->practice->city) }}"
            has-errors="{{ $errors->has('city') }}"
            first-error="{{ $errors->first('city') }}"
            :required="false"
            :is-mat="true"
        ></Cinput>

        <div class="row">
            <div class="col-sm-6">
                <div class="form-group mat">
                    <label for="state">State</label>
                    <select
                        id="state"
                        class="form-control{{ $errors->has('state') ? ' is-invalid' : '' }}"
                        name="state"
                        v-model="state"
                        v-select2
                        ref="select2_state"
                    >
                        @foreach ($states as $state)
                            <option></option>
                            <option
                                @if($state->short_title == old('state', $user->practice->state)) selected @endif
                            value="{{ $state->short_title }}"
                            >{{ $state->title }}</option>
                        @endforeach
                    </select>
                    @if ($errors->has('state'))
                        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('state') }}</strong>
                                    </span>
                    @endif
                </div>
            </div>
            <div class="col-sm-6">
                <Cinput
                    label="Zip"
                    id="zip"
                    type="tel"
                    name="zip"
                    :init-model="zip"
                    init-model-attr="zip"
                    value="{{ old('zip', $user->practice->zip) }}"
                    has-errors="{{ $errors->has('zip') }}"
                    first-error="{{ $errors->first('zip') }}"
                    :required="false"
                    :number-input="true"
                    :is-mat="true"
                    mask="#########"
                ></Cinput>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <Cinput
                    label="Practice URL"
                    id="url"
                    type="text"
                    name="url"
                    :init-model="url"
                    init-model-attr="url"
                    value="{{ old('url', $user->practice->url) }}"
                    has-errors="{{ $errors->has('url') }}"
                    first-error="{{ $errors->first('url') }}"
                    :required="false"
                    :is-mat="true"
                ></Cinput>
            </div>
            <div class="col-md-6">
                <Cinput
                    label="Practice Phone"
                    id="phone"
                    type="text"
                    name="phone"
                    :init-model="phone"
                    init-model-attr="phone"
                    value="{{ old('phone', $user->practice->practice_phone) }}"
                    has-errors="{{ $errors->has('phone') }}"
                    first-error="{{ $errors->first('phone') }}"
                    :required="false"
                    :number-input="true"
                    mask="(###) ###-####"
                    :is-mat="true"
                ></Cinput>
            </div>
        </div>

        <div class="form-group">
            <button type="submit" class="btn form-button" @click="$loading.show()">Continue</button>
        </div>
    </form>
</practice-info>
