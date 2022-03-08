@extends('layouts.main')

@section('content')
    <div class="hire hire-center">
        <industry
                inline-template
                v-cloak
        >
            <div class="centralform">
                <form method="POST" action="{{ route('shifts.createBase', $shift) }}" ref="form">
                    @csrf
                    <div class="inputs">
                        <div class="form-group">
                            <a href="{{ route('shifts.index') }}" @click="$loading.show()" class="back-chevron"><i class="fa fa-chevron-left" aria-hidden="true"></i></a>
                            <span class="title">Please select your Provider type</span>
                            <hr class="full-hr"/>
                            <select
                                    id="position"
                                    class="form-control{{ $errors->has('position') ? ' is-invalid' : '' }}"
                                    name="position"
                                    ref="select2_industry"
                                    v-select2
                                    @change="submitForm()"
                            >
                                <option value=""></option>
                                @foreach ($positions as $position)
                                    @if($position->children->isEmpty())

                                        <option
                                                value="{{ $position->id }}"
                                                @if($position->id == old('position', $shift->position_id ?? null)) selected @endif
                                        >
                                            {{ $position->title }}
                                        </option>
                                    @else
                                        <optgroup label="{{ $position->title }}">
                                            @foreach ($position->children as $child)
                                                <option
                                                    value="{{ $child->id }}"
                                                    @if($child->id == old('position', $shift->position_id ?? null)) selected @endif
                                                >
                                                    {{ $child->title }}
                                                </option>
                                            @endforeach
                                        </optgroup>
                                    @endif
                                @endforeach
                            </select>
                            @if ($errors->has('position'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('position') }}</strong>
                                </span>
                            @endif
                        </div>
                        <input type="hidden" name="now" value="{{ (int)$now }}" />
                    </div>

                    <div class="form-group" v-if="{{ isset($shift->position_id) ? 1 : 0 }}">
                        <button type="submit" class="btn form-button" @click="$loading.show()">Continue</button>
                    </div>
                </form>
                @if ($shift)
                    @include('shift._cancel-form')
                @else
                    <a href="{{ route('shifts.index') }}" class="back-x wtext" data-container="body" data-toggle="popover" data-placement="top" data-content="Cancel shift" data-trigger="hover"><i class="fa fa-times" aria-hidden="true"></i> Cancel</a>
                @endif
            </div>
        </industry>
    </div>
@endsection
