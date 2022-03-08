@extends('layouts.onboarding', ['h2' => "Let's get to know you!"])
@section('content')
    <account-details
        inline-template
        v-cloak
        photo-init="{{ $user->specialist->photo ? $user->specialist->photo_url  : null }}"
    >
        <div>
            <div class="onboarding-cont">
                <div class="text-center d-cropper-div">
                    <div class="img-div">
                        <img v-if="userAvatar" :src="userAvatar">
                        <i v-else class="fa fa-camera"></i>
                    </div>
                    <button type="button" id="pick-avatar" class="btn btn-dark">@{{ userAvatar ? 'Change photo' : 'Click camera icon to upload your photo' }}</button>
                    <avatar-cropper
                        trigger="#pick-avatar"
                        upload-url="{{ route('savePhoto') }}"
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
            </div>
            <form method="POST" action="{{ route('provider.onboarding.photoNext') }}">
                @csrf
                <div class="text-center continue-butt">
                    <button type="submit" class="btn btn-bg-grad">Continue</button>
                    {{--
                    <br/>
                    <a href="{{ route('provider.onboarding.shiftLength') }}" class="later">Add Later</a>
                    --}}
                </div>
            </form>

            @include('register.provider.onboarding._progress', ['percent' => 14])
        </div>
    </account-details>
@endsection
