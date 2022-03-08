@extends('layouts.main')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card">
                    <div class="card-header">Edit speciality {{ $speciality->title }}</div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('admin.data.specialities.update', $speciality) }}">
                            @method('PUT')
                            @csrf

                            <div class="form-group">
                                <label for="title" class="col-form-label">Speciality title</label>
                                <input id="title" class="form-control{{ $errors->has('title') ? ' is-invalid' : '' }}" name="title" value="{{ old('title', $speciality->title) }}">
                                @if ($errors->has('title'))
                                    <span class="invalid-feedback"><strong>{{ $errors->first('title') }}</strong></span>
                                @endif
                            </div>
                            <div class="form-group">
                                <label for="industry" class="col-form-label">Industry</label>
                                <select id="industry" class="form-control" name="industry">
                                    <option value=""></option>
                                    @foreach ($industries as $industry)
                                        <option value="{{ $industry->id }}"{{ $industry->id == request('industry', $speciality->industry_id) ? ' selected' : '' }}>{{ $industry->title }}</option>
                                    @endforeach;
                                </select>
                                @if ($errors->has('industry'))
                                    <span class="invalid-feedback"><strong>{{ $errors->first('industry') }}</strong></span>
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