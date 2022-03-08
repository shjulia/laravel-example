@extends('layouts.main')

@section('content')
    <div class="hire hire-center">
        <shift-location
            inline-template
            v-cloak
        >
            <div class="centralform">
                <form method="POST" action="{{ route('shifts.location', $shift) }}" ref="form">
                    @csrf
                    <div class="inputs">
                        <div class="form-group">
                            <a href="{{ route('shifts.base', $shift) }}" @click="$loading.show()" class="back-chevron"><i class="fa fa-chevron-left" aria-hidden="true"></i></a>
                            <span class="title">Please select location for shift</span>
                            <hr class="full-hr"/>
                            <select
                                id="location"
                                class="form-control{{ $errors->has('location') ? ' is-invalid' : '' }}"
                                name="location"
                                ref="select2_location"
                                v-select2
                                @change="submitForm()"
                            >
                                <option value="">Main location ({{ $practice->full_address }})</option>
                                @foreach ($practice->addresses as $address)
                                    <option
                                        value="{{ $address->id }}"
                                        @if($address->id == old('location', $shift->location_id ?? null)) selected @endif
                                    >
                                        {{ $address->full_address }}
                                    </option>
                                @endforeach
                            </select>
                            @if ($errors->has('location'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('location') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn form-button" @click="$loading.show()">Continue</button>
                    </div>
                </form>
                @include('shift._cancel-form')
            </div>
        </shift-location>
    </div>
@endsection
