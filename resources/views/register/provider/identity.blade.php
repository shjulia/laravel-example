@extends('layouts.auth')

@section('content')
    <div class="wraper wraper-login">
        <div class="auth-container">
            <div class="auth-form step-user-base">
                @include('partials._form-titles', [
                    'h1' => 'Identity',
                    'desc' => 'Please enter your identity Information.',
                    'backUrl' => route('signup.industry', ['code' => $user->tmp_token])
                ])

                @include("register.provider._stepper", [
                    'active' => 'identity'
                ])
                @include("register.provider._identity-forms", [
                    'action' => route('signup.uploadDriver', ['code' => $user->tmp_token]),
                    'nextAction' => route('signup.license', ['code' => $user->tmp_token]),
                    'phoneAction' => route('signup.phoneSave', ['code' => $user->tmp_token]),
                    'formAction' => route('signup.identitySave', ['code' => $user->tmp_token])
                ])
            </div>
        </div>
    </div>
@endsection
