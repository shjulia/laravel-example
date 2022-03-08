@extends('layouts.onboarding', ['h2' => "What are your specialties?"])
@section('content')
    <form method="POST" action="{{ route('provider.onboarding.specialities') }}">
        @csrf
        <div class="onboarding-cont">
            <bubble-input
                name="specialities"
                :bubbles="{{ $specialities }}"
                :marks-init="{{ collect($specialistSpecialities) }}"
                error="{{ $errors->first('specialities') }}"
            ></bubble-input>
        </div>
        <div class="text-center continue-butt">
            <button type="submit" class="btn btn-bg-grad">Continue</button>
            <br/>
            <a href="{{ route('provider.onboarding.availability') }}" class="later">Add Later</a>
        </div>
        @include('register.provider.onboarding._progress', ['percent' => 71])
    </form>
@endsection
