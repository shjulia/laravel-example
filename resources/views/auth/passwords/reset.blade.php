@extends('layouts.auth')

@section('content')
    <div class="wraper wraper-login">
        <div class="auth-container">
            <div class="auth-form step-user-base">
                @include('partials._form-titles', [
                    'h1' => 'Set Password',
                    'desc' => 'Please enter your new password',
                    'backUrl' => url('/login')
                ])
                <form method="POST" action="{{ route('password.update') }}">
                    @csrf
                    <input type="hidden" name="token" value="{{ $token }}">

                    <Cinput
                            label="E-mail Address"
                            id="email"
                            type="email"
                            name="email"
                            value="{{ old('email') }}"
                            has-errors="{{ $errors->has('email') }}"
                            first-error="{{ $errors->first('email') }}"
                            :required="false"
                            :is-mat="true"
                            :prepend="true"
                            prepend-icon="user"
                    ></Cinput>

                    <Cinput
                            label="Password"
                            id="password"
                            type="password"
                            name="password"
                            value="{{ old('password') }}"
                            has-errors="{{ $errors->has('password') }}"
                            first-error="{{ $errors->first('password') }}"
                            :required="false"
                            :is-mat="true"
                            :prepend="true"
                            prepend-icon="lock"
                            autocomplete="new-password"
                    ></Cinput>

                    <Cinput
                            label="Confirm Password"
                            id="password_confirmation"
                            type="password"
                            name="password_confirmation"
                            value="{{ old('password_confirmation') }}"
                            has-errors="{{ $errors->has('password_confirmation') }}"
                            first-error="{{ $errors->first('password_confirmation') }}"
                            :required="false"
                            :is-mat="true"
                            :prepend="true"
                            prepend-icon="lock"
                            autocomplete="new-password"
                    ></Cinput>

                    <div class="form-group">
                        <button type="submit" class="btn form-button">Set Password</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
