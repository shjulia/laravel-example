@extends('layouts.main')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card">
                    <div class="card-header">Create new position</div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('admin.data.positions.store') }}">
                            @csrf

                            <div class="form-group">
                                <label for="title" class="col-form-label">Position title</label>
                                <input id="title" class="form-control{{ $errors->has('title') ? ' is-invalid' : '' }}" name="title" value="{{ old('title') }}" required>
                                @if ($errors->has('title'))
                                    <span class="invalid-feedback"><strong>{{ $errors->first('title') }}</strong></span>
                                @endif
                            </div>
                            <div class="form-group">
                                <label for="industry" class="col-form-label">Industry</label>
                                <select id="industry" class="form-control" name="industry">
                                    <option value=""></option>
                                    @foreach ($industries as $industry)
                                        <option value="{{ $industry->id }}"{{ $industry->id == request('industry') ? ' selected' : '' }}>{{ $industry->title }}</option>
                                    @endforeach;
                                </select>
                                @if ($errors->has('industry'))
                                    <span class="invalid-feedback"><strong>{{ $errors->first('industry') }}</strong></span>
                                @endif
                            </div>
                            <div class="form-group">
                                <label for="parent_id" class="col-form-label">Parent position</label>
                                <select id="parent_id" class="form-control" name="parent_id">
                                    <option value=""></option>
                                    @foreach ($positionsList as $pos)
                                        <option value="{{ $pos->id }}"{{ $pos->id == request('position_id') ? ' selected' : '' }}>{{ $pos->title }}</option>
                                    @endforeach;
                                </select>
                                @if ($errors->has('parent_id'))
                                    <span class="invalid-feedback"><strong>{{ $errors->first('parent_id') }}</strong></span>
                                @endif
                            </div>
                            <div class="form-group">
                                <label for="fee" class="col-form-label">Fee</label>
                                <input id="fee" class="form-control{{ $errors->has('fee') ? ' is-invalid' : '' }}" name="fee" value="{{ old('fee') }}" required>
                                @if ($errors->has('fee'))
                                    <span class="invalid-feedback"><strong>{{ $errors->first('fee') }}</strong></span>
                                @endif
                            </div>
                            <div class="form-group">
                                <label for="minimum_profit" class="col-form-label">Minimum profit</label>
                                <input id="minimum_profit" class="form-control{{ $errors->has('minimum_profit') ? ' is-invalid' : '' }}" name="minimum_profit" value="{{ old('minimum_profit') }}" required>
                                @if ($errors->has('minimum_profit'))
                                    <span class="invalid-feedback"><strong>{{ $errors->first('minimum_profit') }}</strong></span>
                                @endif
                            </div>
                            <div class="form-group">
                                <label for="surge_price" class="col-form-label">Surge price</label>
                                <input id="surge_price" class="form-control{{ $errors->has('surge_price') ? ' is-invalid' : '' }}" name="surge_price" value="{{ old('surge_price') }}" required>
                                @if ($errors->has('surge_price'))
                                    <span class="invalid-feedback"><strong>{{ $errors->first('surge_price') }}</strong></span>
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
