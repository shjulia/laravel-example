@extends('layouts.main')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card">
                    <div class="card-header">Create new task</div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('admin.data.tasks.store') }}">
                            @csrf
                            <div class="form-group">
                                <label for="title" class="col-form-label">Task title</label>
                                <input id="title" class="form-control{{ $errors->has('title') ? ' is-invalid' : '' }}" name="title" value="{{ old('title') }}" required>
                                @if ($errors->has('title'))
                                    <span class="invalid-feedback"><strong>{{ $errors->first('title') }}</strong></span>
                                @endif
                            </div>
                            <div class="form-group">
                                <label for="position" class="col-form-label">Position</label>
                                <select id="position" class="form-control" name="position">
                                    <option value=""></option>
                                    @foreach ($positions as $position)
                                        <option value="{{ $position->id }}"{{ $position->id == request('position') ? ' selected' : '' }}>{{ $position->title }}</option>
                                    @endforeach;
                                </select>
                                @if ($errors->has('position'))
                                    <span class="invalid-feedback"><strong>{{ $errors->first('position') }}</strong></span>
                                @endif
                            </div>

                            <div class="form-group text-center">
                                <button type="submit" class="btn btn-primary">Save</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection