@extends('layouts.main')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-3">
                <div class="card">
                    <div class="card-header">Shift {{ $shift->id }}</div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('admin.shifts.update', $shift) }}">
                            @method('PUT')
                            @csrf

                            <div class="form-group">
                                <label for="provider_id" class="col-form-label">Provider id</label>
                                <input id="provider_id" class="form-control{{ $errors->has('provider_id') ? ' is-invalid' : '' }}" name="provider_id" value="{{ old('provider_id', $shift->provider_id) }}" required>
                                @if ($errors->has('provider_id'))
                                    <span class="invalid-feedback"><strong>{{ $errors->first('provider_id') }}</strong></span>
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