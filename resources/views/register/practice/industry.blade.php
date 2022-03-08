@extends('layouts.auth')

@section('content')
    <div class="wraper wraper-login">
        <div class="auth-container">
            <div class="auth-form step-user-base">
                @include('partials._form-titles', [
                    'h1' => 'Select Industry',
                    'desc' => 'Please enter your industry Information.',
                ])

                @include("register.practice._stepper", [
                    'active' => 'industry'
                ])
                <industry
                    inline-template
                    v-cloak
                >
                    <form method="POST" action="{{ route('practice.signup.industrySave', ['code' => $user->tmp_token]) }}">
                        @csrf
                        <div class="form-group mat">
                            <label for="industry">Select Industry</label>
                            <select
                                    id="industry"
                                    class="form-control{{ $errors->has('industry') ? ' is-invalid' : '' }}"
                                    name="industry"
                                    ref="select2_industry"
                                    v-select2
                            >
                                <option value=""></option>
                                @foreach ($industries as $industry)
                                    <option
                                            value="{{ $industry->id }}"
                                            @if($industry->id == old('industry', $user->practice->industry_id)) selected @endif
                                    >
                                        {{ $industry->title }}
                                    </option>
                                @endforeach
                            </select>
                            @if ($errors->has('industry'))
                                <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('industry') }}</strong>
                            </span>
                            @endif
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn form-button" @click="$loading.show()">Continue</button>
                        </div>
                    </form>
                </industry>
            </div>
        </div>
    </div>
@endsection
