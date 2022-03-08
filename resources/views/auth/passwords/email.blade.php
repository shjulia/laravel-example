@extends('layouts.auth')

@section('content')
    <div class="wraper wraper-login">
        <div class="auth-container">
            <div class="auth-form step-user-base">
                @include('partials._form-titles', [
                    'h1' => 'Recovery Password',
                    'desc' => 'Please enter your valid email address',
                    'backUrl' => url('/login')
                ])

                <form method="POST" action="{{ route('password.email') }}" @submit="$loading.show()">
                    @csrf

                    @if (session('localsuccess'))
                        @include('partials._local-alert')
                    @else
                        <div class="form-group">
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
                                    prepend-icon="envelope-o"
                            ></Cinput>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn form-button">Reset Password</button>
                        </div>
                    @endif
                </form>
            </div>
        </div>
    </div>
@endsection
