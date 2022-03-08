@extends('layouts.auth')

@section('content')
    <div class="wraper wraper-login">
        <div class="auth-container">
            <div class="auth-form step-user-base">
                @include('partials._form-titles', [
                    'h1' => 'Licensure',
                    'desc' => 'Please enter your medical licenses information',
                    'backUrl' => route('signup.identity', ['code' => $user->tmp_token])
                ])

                @include("register.provider._stepper", [
                    'active' => 'licenses'
                ])
                @include('license._form', [
                    'uploadPhotoUrl' => route('signup.uploadMedical', ['code' => $user->tmp_token]),
                    'action' => route('signup.licenseSave', ['code' => $user->tmp_token]),
                    'removeLicenseUrl' => route('signup.removeLicense', ['code' => $user->tmp_token]),
                    'saveOneAction' => route('signup.oneLicenseSave', ['code' => $user->tmp_token])
                ])
                <div class="text-center">
                    Don't have these documents handy?
                    <br>
                    <a href="{{ route('signup.check', ['code' => $user->tmp_token]) }}">Add them later</a>
                </div>
            </div>
        </div>
    </div>
@endsection
