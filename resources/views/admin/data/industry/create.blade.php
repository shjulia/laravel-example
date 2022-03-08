@extends('layouts.main')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card">
                    <div class="card-header">Create new industry</div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('admin.data.industries.store') }}">
                            @csrf

                            <div class="form-group">
                                <label for="title" class="col-form-label">Industry title</label>
                                <input id="title" class="form-control{{ $errors->has('title') ? ' is-invalid' : '' }}" name="title" value="{{ old('title') }}" required>
                                @if ($errors->has('title'))
                                    <span class="invalid-feedback"><strong>{{ $errors->first('title') }}</strong></span>
                                @endif
                            </div>

                            <div class="form-group">
                                <label for="alias" class="col-form-label">Industry alias</label>
                                <input id="alias" class="form-control{{ $errors->has('alias') ? ' is-invalid' : '' }}" name="alias" value="{{ old('alias') }}" required>
                                @if ($errors->has('alias'))
                                    <span class="invalid-feedback"><strong>{{ $errors->first('alias') }}</strong></span>
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