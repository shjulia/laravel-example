@extends('layouts.main')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card">
                    <div class="card-header">Edit score bubble {{ $score->title }}</div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('admin.data.scores.update', $score) }}">
                            @csrf
                            @method('PUT')

                            <div class="form-group">
                                <label for="title" class="col-form-label">Score bubble title</label>
                                <input id="title" class="form-control{{ $errors->has('title') ? ' is-invalid' : '' }}" name="title" value="{{ old('title', $score->title) }}" required>
                                @if ($errors->has('title'))
                                    <span class="invalid-feedback"><strong>{{ $errors->first('title') }}</strong></span>
                                @endif
                            </div>
                            <div class="form-group">
                                <label for="for_type" class="col-form-label">For type</label>
                                <select id="for_type" class="form-control" name="for_type">
                                    <option value=""></option>
                                    <option value="practice"{{ "practice" == old('for_type', $score->for_type) ? ' selected' : '' }}>practice</option>
                                    <option value="provider"{{ "provider" == old('for_type', $score->for_type) ? ' selected' : '' }}>provider</option>
                                </select>
                                @if ($errors->has('for_type'))
                                    <span class="invalid-feedback"><strong>{{ $errors->first('for_type') }}</strong></span>
                                @endif
                            </div>
                            <div class="form-group">
                                <div class="custom-control custom-checkbox mb-4">
                                    <input type="checkbox" class="custom-control-input" id="active" name="active" value="1" @if(old('active', $score->active)) checked="checked" @endif>
                                    <label class="custom-control-label" for="active">Active</label>
                                </div>
                                @if ($errors->has('active'))
                                    <span class="invalid-feedback"><strong>{{ $errors->first('active') }}</strong></span>
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