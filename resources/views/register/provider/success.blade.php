@extends('layouts.auth')

@section('content')
    <div class="wraper wraper-login">
        <div class="auth-container">
            <div class="auth-form step-user-base">
                @include('partials._form-titles', ['h1' => '', 'desc' => ''])

                <i class="fa fa-check-circle-o congrat-circle" aria-hidden="true"></i>
                <h1>Congratulations</h1>
                <p class="h1_subtitle">Your account is set-up. You will be notified when you're cleared to work. In the meantime, let's get to know each other.</p>
                <div class="form-group">
                    <a href="{{ route('provider.onboarding.photo') }}" class="btn form-button" @click="$loading.show()">Continue</a>
                </div>
                <success
                    inline-template
                    email="{{ $user->email }}"
                    type="provider"
                >
                    <div></div>
                </success>
            </div>
        </div>
    </div>
@endsection
