@extends('layouts.main')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-7">
                <div class="card">
                    <div class="card-header">Invite {{ $invite->email }}</div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('admin.users.edit.invite', $invite) }}">
                            @csrf

                            <div class="form-group">
                                <label for="payment_system" class="col-form-label">Payment system</label>
                                <select id="payment_system" class="form-control" name="payment_system">
                                    <option value=""></option>
                                    @foreach ($systems as $system)
                                        <option value="{{ $system }}"{{ $system == request('payment_system', $invite->payment_system) ? ' selected' : '' }}>{{ $system }}</option>
                                    @endforeach;
                                </select>
                                @if ($errors->has('payment_system'))
                                    <span class="invalid-feedback"><strong>{{ $errors->first('payment_system') }}</strong></span>
                                @endif
                            </div>

                            <div class="form-group">
                                <label for="status" class="col-form-label">Payment status</label>
                                <select id="status" class="form-control" name="status">
                                    <option value=""></option>
                                    @foreach ($statuses as $status)
                                        <option value="{{ $status }}"{{ $status == request('status', $invite->status) ? ' selected' : '' }}>{{ $status }}</option>
                                    @endforeach;
                                </select>
                                @if ($errors->has('status'))
                                    <span class="invalid-feedback"><strong>{{ $errors->first('status') }}</strong></span>
                                @endif
                            </div>

                            <div class="form-group">
                                <label for="charge_id" class="col-form-label">Payment system charge id</label>
                                <input id="charge_id" class="form-control{{ $errors->has('charge_id') ? ' is-invalid' : '' }}" name="charge_id" value="{{ old('charge_id', $invite->charge_id) }}">
                                @if ($errors->has('charge_id'))
                                    <span class="invalid-feedback"><strong>{{ $errors->first('charge_id') }}</strong></span>
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
