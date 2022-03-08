@extends('layouts.auth')

@section('content')
    <div class="wraper wraper-login">
        <div class="auth-container">
            <div class="auth-form step-user-base">
                @include('partials._form-titles', [
                    'h1' => 'Insurance Details',
                    'desc' => 'Please enter your insurance details.',
                    'backUrl' => route('practice.signup.base', ['code' => $user->tmp_token])
                ])

                @include("register.practice._stepper", [
                    'active' => 'insurance'
                ])
                @include('register.practice._insurance-form', [
                    'action' => route('practice.signup.insuranceSave', ['code' => $user->tmp_token]),
                    'uploadUrl' => route('practice.signup.uploadInsurance', ['code' => $user->tmp_token]),
                    'removeUrl' => route('practice.signup.removeInsurance', ['code' => $user->tmp_token])
                ])
            </div>
        </div>
    </div>
@endsection
