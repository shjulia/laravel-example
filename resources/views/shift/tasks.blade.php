@extends('layouts.main')

@section('content')
    <div class="hire hire-center">
        <shifttasks
                inline-template
                settasks-init="{{ old('settasks', $shift->tasks ? true : false) }}"
                has-same-time="{{ $hasSameTime }}"
                previous="{{ route('shifts.time', ['shift' => $shift]) }}"
                v-cloak
        >
            <div class="centralform">
                <form method="POST" action="{{ route('shifts.setTasks', $shift) }}">
                    @csrf
                    <div class="inputs">
                        <a href="{{ route('shifts.time', $shift) }}" @click="$loading.show()" class="back-chevron"><i class="fa fa-chevron-left" aria-hidden="true"></i></a>
                        <span class="title">Would You like to select what your {{ $shift->position->title }} will be doing?</span>
                        <hr class="full-hr"/>

                        <div class="form-group row">
                            <label class="col-sm-10 col-form-label holiday-label">Select tasks</label>
                            <div class="col-sm-2 custom-control custom-switch mt-2">
                                <input type="checkbox" id="settasks" class="custom-control-input" value="1" name="settasks" @if(old('settasks', !empty($shift->tasks))) checked @endif v-model="settasks">
                                <label class="custom-control-label" for="settasks"> </label>
                            </div>
                        </div>

                        <div class="form-group" v-if="settasks">
                            <select multiple="multiple" name="tasks[]" class="select2 form-control{{ $errors->has('tasks') ? ' is-invalid' : '' }}" ref="select2">
                                @foreach($tasks as $task)
                                    <option
                                        value="{{ $task->id }}"
                                        @if(in_array($task->id, old('tasks', $shift->tasks ?: []))) selected @endif
                                    >{{ $task->title }}</option>
                                @endforeach
                            </select>
                            @if ($errors->has('tasks'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('tasks') }}</strong>
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
        </shifttasks>
    </div>
@endsection
