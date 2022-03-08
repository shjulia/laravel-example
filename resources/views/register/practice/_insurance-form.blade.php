<insurance
    inline-template
    showforminit="{{ $errors->any() || isset($user->practice->policy_photo) }}"
    action="{{ $uploadUrl }}"
    old-file-init="{{ isset($user->practice->policy_photo) ? $user->practice->policy_photo_url : null }}"
    remove-action="{{ $removeUrl }}"
    v-cloak
>
    <div class="row">
        <div class="col-md-12">
            <form ref="photoform">
                <div class="input-group">
                    <div class="custom-file">
                        <input type="file" name="photo" class="custom-file-input" @change="onChange()">
                        <label class="custom-file-label little-custom-file-label">Choose file of insurance policy</label>
                    </div>
                    <div class="input-group-append fileinput-button" v-if="oldFile">
                        <button @click="remove()" class="btn btn-outline-secondary" type="button">Remove</button>
                        <button @click="showFile()" class="btn btn-outline-primary" type="button">Show file</button>
                    </div>
                </div>
                <div class="invalid-feedback" v-if="server_errors.photo">@{{ server_errors.photo }}</div>
            </form>
        </div>
        <div class="col-md-12 text-center mt-2">
            <a class="boon-link" href="#" @click.prevent="manualy">OR manually input the information</a>
        </div>

        <div class="col-md-12">
            <br/>
            <form method="POST" action="{{ $action }}">
                @csrf
                <div v-show="showForm">
                    <Cinput
                        label="Policy Type"
                        id="type"
                        type="text"
                        name="type"
                        value="{{ old('type', $user->practice->policy_type) }}"
                        has-errors="{{ $errors->has('type') }}"
                        first-error="{{ $errors->first('type') }}"
                        :required="false"
                        :is-mat="true"
                    ></Cinput>

                    <Cinput
                        label="Policy Number"
                        id="number"
                        type="text"
                        name="number"
                        value="{{ old('number', $user->practice->policy_number) }}"
                        has-errors="{{ $errors->has('number') }}"
                        first-error="{{ $errors->first('number') }}"
                        :required="false"
                        :is-mat="true"
                    ></Cinput>

                    <Cinput
                        label="Policy Expiration"
                        id="expiration_date"
                        type="date"
                        name="expiration_date"
                        value="{{ old('expiration_date', $user->practice->policy_expiration_date ? $user->practice->policy_expiration_date->format('Y-m-d') : null) }}"
                        has-errors="{{ $errors->has('expiration_date') }}"
                        first-error="{{ $errors->first('expiration_date') }}"
                        :required="false"
                        :is-mat="true"
                    ></Cinput>

                    <Cinput
                        label="Policy Provider (company)"
                        id="provider"
                        type="text"
                        name="provider"
                        value="{{ old('provider', $user->practice->policy_provider) }}"
                        has-errors="{{ $errors->has('provider') }}"
                        first-error="{{ $errors->first('provider') }}"
                        :required="false"
                        :is-mat="true"
                    ></Cinput>
                </div>

                <div class="form-group">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" value="1" id="no_policy" name="no_policy" @if(old('no_policy', $user->practice->no_policy)) checked @endif v-model="no_policy">
                        <label class="custom-control-label" for="no_policy">We Do Not Have Practice Insurance</label>
                        @if ($errors->has('no_policy'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('no_policy') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>

                <div class="form-group">
                    <button type="submit" class="btn form-button" @click="$loading.show()">Continue</button>
                </div>
            </form>
        </div>
    </div>
</insurance>
