@extends('layouts.onboarding', ['h2' => "What is your minimum rate?"])
@section('content')
    <form method="POST" action="{{ route('provider.onboarding.rate') }}">
        @csrf
        <div class="onboarding-cont">
            <div class="rate-div">
                <Cinput
                    label=""
                    id="rate"
                    type="text"
                    name="rate"
                    has-errors="{{ $errors->has('rate') }}"
                    first-error="{{ $errors->first('rate') }}"
                    value="{{ old('rate', $user->specialist->min_rate ?: $positionRate) }}"
                    :prepend="true"
                    prepend-icon="usd"
                ></Cinput>
            </div>
        </div>
        <div class="text-center continue-butt">
            <button type="submit" class="btn btn-bg-grad">Continue</button>
            <br/>
            <a href="{{ route('provider.onboarding.tool') }}" class="later">Add Later</a>
        </div>
        @include('register.provider.onboarding._progress', ['percent' => 71])
    </form>
@endsection
