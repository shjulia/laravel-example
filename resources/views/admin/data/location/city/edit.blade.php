@extends('layouts.main')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card">
                    <div class="card-header">Edit {{ $city->name }}</div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('admin.data.location.city.update', [$state, $city]) }}">
                            @csrf
                            @method('PUT')

                            <div class="form-group">
                                <label for="name" class="col-form-label">Name</label>
                                <input id="name" class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}" name="name" value="{{ old('name', $city->name) }}" required>
                                @if ($errors->has('name'))
                                    <span class="invalid-feedback"><strong>{{ $errors->first('name') }}</strong></span>
                                @endif
                            </div>
                            <div class="form-group">
                                <label for="tier" class="col-form-label">Tier</label>
                                <input id="tier" class="form-control{{ $errors->has('tier') ? ' is-invalid' : '' }}" name="tier" value="{{ old('tier', $city->tier) }}" required>
                                @if ($errors->has('tier'))
                                    <span class="invalid-feedback"><strong>{{ $errors->first('tier') }}</strong></span>
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