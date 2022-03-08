@extends('layouts.onboarding', ['h2' => "What's your holiday availability?"])
@section('content')
    <holidays-availability
        inline-template
        show-holidays-init="{{ !!old('show-holidays', count($user->specialist->additional->holidays)) }}"
    >
        <form method="POST" action="{{ route('provider.onboarding.holidays') }}">
            @csrf
            <div class="onboarding-cont">
                <div class="form-group row">
                    <label class="col-9 col-sm-10 col-form-label holiday-label">Available at holidays</label>
                    <div class="col-3 col-sm-2 custom-control custom-switch">
                        <input type="checkbox" id="show-holidays" class="custom-control-input" value="1" name="show-holidays" @if(old('show-holidays', count($user->specialist->additional->holidays))) checked @endif v-model="showHolidays">
                        <label class="custom-control-label" for="show-holidays"> </label>
                    </div>
                </div>

                <hr/>

                <div v-if="showHolidays">
                    <h4 class="detailsh4 holiday-label">Availability</h4>
                    <div class="holidays-div">
                        @foreach($holidays as $holiday)
                            <div class="form-group row">
                                <label class="col-9 col-sm-10 col-form-label holiday-label">{{ $holiday->title }}</label>
                                <div class="col-3 col-sm-2 custom-control custom-checkbox text-right">
                                    <input type="checkbox" class="custom-control-input" id="holiday[{{$holiday->id}}]" name="holiday[{{$holiday->id}}]" value="1" @if(old('holiday.' . $holiday->id, $user->specialist->additional->holidays[$holiday->id] ?? false)) checked @endif>
                                    <label class="custom-control-label" for="holiday[{{$holiday->id}}]"> </label>
                                    @if ($errors->has('holiday.' . $holiday->id))
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $errors->first('holiday.' . $holiday->id) }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                    @if ($errors->has('holiday'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('holiday') }}</strong>
                        </span>
                    @endif
                </div>
            </div>
            <div class="text-center continue-butt">
                <button type="submit" class="btn btn-bg-grad">Continue</button>
                <br/>
                <a href="{{ route('home') }}" class="later">Add Later</a>
            </div>
            @include('register.provider.onboarding._progress', ['percent' => 100])
        </form>
    </holidays-availability>
@endsection
