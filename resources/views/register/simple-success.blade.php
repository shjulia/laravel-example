@extends('layouts.auth')

@section('content')
    <div class="wraper wraper-login">
        <div class="auth-container">
            <div class="auth-form step-user-base">
                @include('partials._form-titles', ['h1' => '', 'desc' => ''])

                <i class="fa fa-check-circle-o congrat-circle" aria-hidden="true"></i>
                <h1>Congratulations</h1>
                <p class="h1_subtitle">Your account was created. You can finish sign-up process later or continue now.</p>
                <div class="form-group">
                    <a href="{{ $route }}" class="btn form-button">Continue</a>
                </div>
            </div>
        </div>
    </div>
@endsection
