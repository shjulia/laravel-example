@extends('layouts.auth')

@section('content')
<div class="wraper wraper-login">
    <div class="auth-container">
        <div class="auth-form">
            @include('partials._form-titles', [
                'h1' => 'Sign in to boon',
                'desc' => 'Please enter your credentials to proceed.'
            ])
            <form method="POST" action="{{ route('login') }}" autocomplete="off" v-cloak>
                @csrf

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

                <div class="form-group">
                    <div class="custom-control custom-checkbox">
                        <input class="form-check-input custom-control-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                        <label class="custom-control-label" for="remember">Remember Me</label>
                    </div>
                </div>
                <div class="form-group forgot">
                    <a href="{{ route('password.request') }}">Forgot Password</a>
                </div>

                <div class="form-group">
                    <button type="submit" class="btn form-button">Login</button>
                    <p class="after-button">Don't have an account? <a class="boon-link" href="{{ route('signup.userBase') }}">Click here.</a></p>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
