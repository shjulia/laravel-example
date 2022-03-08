@extends('layouts.auth')

@section('content')
    <div class="wraper wraper-login">
        <div class="auth-container">
            <div class="auth-form step-user-base">
                @include('partials._form-titles', [
                    'h1' => 'Setup practice information',
                    'desc' => 'Please enter your practice information.',
                    'backUrl' => null //route('practice.signup.industry', ['code' => $user->tmp_token])
                ])

                @include("register.practice._stepper", [
                    'active' => 'info'
                ])
                @include("register.practice._base-form", [
                    'action' => route('practice.signup.baseSave', ['code' => $user->tmp_token])
                ])
            </div>
        </div>
    </div>
@endsection
