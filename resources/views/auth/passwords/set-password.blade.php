@extends('layouts.auth')

@section('content')
    <div class="wraper wraper-login">
        <div class="auth-container">
            <div class="auth-form step-user-base">
                @include('partials._form-titles', [
                    'h1' => 'Set password',
                    'desc' => 'Please set your password.'
                ])
                <form method="POST" action="{{ route('set-password-save') }}" autocomplete="off">
                    @csrf

                    <input type="hidden" name="token" value="{{ $token }}">

                    <Cinput
                            label="Mobile Phone"
                            id="phone"
                            type="tel"
                            name="phone"
                            value="{{ old('phone', ' ') }}"
                            has-errors="{{ $errors->has('phone') }}"
                            first-error="{{ $errors->first('phone') }}"
                            :required="false"
                            :is-mat="true"
                            :prepend="true"
                            prepend-icon="mobile big"
                            mask="(###) ###-####"
                            :number-input="true"
                            autocomplete="off"
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
                            autocomplete="off"
                    ></Cinput>
                    <div class="form-group">
                        <button type="submit" class="btn form-button">Continue</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
