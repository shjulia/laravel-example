@extends('layouts.onboarding', ['h2' => "What's your ideal shift length?"])
@section('content')
    <shift-length
        inline-template
        :min-init="{{ $user->specialist->shift_length_min ?: 2 }}"
        :max-init="{{ $user->specialist->shift_length_max ?: 9 }}"
        v-cloak
    >
        <form method="POST" action="{{ route('provider.onboarding.shiftLength') }}">
            @csrf
            <div class="onboarding-cont">
                <div class="row range-input">
                    <div class="col-12 mt-5">
                        <vue-slider v-model="lengthVal" :min="2" :tooltip="'always'" :max="12"></vue-slider>
                        <input type="hidden" name="min" :value="minVal" />
                        <input type="hidden" name="max" :value="maxVal" />
                    </div>
                    <div class="col-6">
                        <span class="mark-title">2 hrs</span>
                        <br/>
                        <span class="mark-desc">minimum</span>
                    </div>
                    <div class="col-6 text-right">
                        <span class="mark-title">12 hrs</span>
                        <br/>
                        <span class="mark-desc">maximum</span>
                    </div>
                </div>
                @if ($errors->has('min'))
                    <span class="invalid-feedback" role="alert">
                    <strong>{{ $errors->first('min') }}</strong>
                </span>
                @endif
                @if ($errors->has('max'))
                    <span class="invalid-feedback" role="alert">
                    <strong>{{ $errors->first('max') }}</strong>
                </span>
                @endif
            </div>

            <div class="text-center continue-butt">
                <button type="submit" class="btn btn-bg-grad">Continue</button>
                <br/>
                <a href="{{ route('provider.onboarding.distance') }}" class="later">Add Later</a>
            </div>
            @include('register.provider.onboarding._progress', ['percent' => 28])
        </form>
    </shift-length>
@endsection
