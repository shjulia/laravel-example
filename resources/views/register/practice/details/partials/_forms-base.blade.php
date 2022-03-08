<pbase-details
    photo-init="{{ $user->practice->practice_photo ? $user->practice->practice_photo_url : null }}"
    inline-template
    v-cloak
>
    <div>
        @if ($showLinks)
            <a href="{{ url('/') }}" @click="$loading.show()" class="back"><i class="fa fa-chevron-left"></i> BACK</a>
        @endif
        <h2 class="detailsh2">Update Your Profile</h2>
        <div class="text-center d-cropper-div">
            <div class="img-div">
                <img v-if="userAvatar" :src="userAvatar">
                <i v-else class="fa fa-camera"></i>
            </div>
            <button id="pick-avatar" class="btn btn-success">@{{ userAvatar ? 'Change photo' : 'Select photo' }}</button>
            <avatar-cropper
                trigger="#pick-avatar"
                upload-url="{{ $photoUploadUrl }}"
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

        <form method="POST" ref="baseform" action="{{ $formAction }}" enctype="multipart/form-data">
            @csrf
            {{--<div class="croppa-div">
                <croppa
                        v-model="myCroppa"
                        :height="200"
                        :width="200"
                        :canvas-color="'#d1fdec'"
                        :placeholder="'Drag practice front photo'"
                        :remove-button-color="'#56dea8'"
                        :remove-button-size="25"
                        :placeholder-font-size="18"
                        initial-image="{{ $user->practice->practice_photo ? $user->practice->practice_photo_url : null  }}"
                        @file-choose="changed()"
                        @move="changed()"
                        @zoom="changed()"
                        data-toggle="tooltip"
                        data-placement="top"
                        title="You can resize and crop image"
                ></croppa>
                <input type="hidden" name="photo" v-model="image">
            </div>
            @if ($errors->has('photo'))
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $errors->first('photo') }}</strong>
                </span>
            @endif--}}
            <h2 class="detailsh2">Practice Details</h2>
            <div class="form-group">
                <label for="culture" class="col-form-label">Tell us about your practice culture</label>
                <textarea id="culture" class="form-control{{ $errors->has('culture') ? ' is-invalid' : '' }}" name="culture" rows="2">{{ old('culture', $user->practice->culture) }}</textarea>
                @if ($errors->has('culture'))
                    <span class="invalid-feedback"><strong>{{ $errors->first('culture') }}</strong></span>
                @endif
            </div>

            <div class="form-group">
                <label for="notes" class="col-form-label">Are there any special notes a provider should know about your practice?</label>
                <textarea id="notes" class="form-control{{ $errors->has('notes') ? ' is-invalid' : '' }}" name="notes" rows="2">{{ old('notes', $user->practice->notes) }}</textarea>
                @if ($errors->has('notes'))
                    <span class="invalid-feedback"><strong>{{ $errors->first('notes') }}</strong></span>
                @endif
            </div>

            <div class="form-group">
                <label for="on_site_contact" class="col-form-label">Who is the Provider’s On-site Point of Contact?</label>
                <input id="on_site_contact" type="text" class="form-control{{ $errors->has('on_site_contact') ? ' is-invalid' : '' }}" name="on_site_contact" value="{{ old('on_site_contact', $user->practice->on_site_contact) }}">
                @if ($errors->has('on_site_contact'))
                    <span class="invalid-feedback"><strong>{{ $errors->first('on_site_contact') }}</strong></span>
                @endif
            </div>

            <div class="form-group row">
                @if ($showLinks)
                    <div class="col-6 col-sm-6">
                        <a href="{{ route('practice.details.secondary') }}" @click="$loading.show()" class="btn form-button skip-button">Skip</a>
                    </div>
                @endif
                <div class="col-6 col-sm-6">
                    <button type="submit" class="btn form-button" @click="$loading.show()">Continue</button>
                </div>
            </div>

        </form>
    </div>

</pbase-details>
