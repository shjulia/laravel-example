@extends('layouts.onboarding', ['h2' => "What routine tasks are you comfortable performing?"])
@section('content')
    <form method="POST" action="{{ route('provider.onboarding.tasks') }}">
        @csrf
        <div class="onboarding-cont">
            <bubble-input
                name="tasks"
                :bubbles="{{ $routineTasks }}"
                :marks-init="{{ collect($specialistTasks) }}"
                error="{{ $errors->first('tasks') }}"
            ></bubble-input>
        </div>
        <div class="text-center continue-butt">
            <button type="submit" class="btn btn-bg-grad">Continue</button>
            <br/>
            <a href="{{ route('provider.onboarding.specialities') }}" class="later">Add Later</a>
        </div>
        @include('register.provider.onboarding._progress', ['percent' => 57])
    </form>
@endsection
