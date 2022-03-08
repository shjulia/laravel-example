@extends('layouts.main')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-3">
                <div class="card">
                    <div class="card-header">Set inviter to {{ $user->full_name }}</div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('admin.users.setInviter', $user) }}">
                            @csrf

                            <div class="form-group">
                                <label for="user_id" class="col-form-label">Inviter id</label>
                                <input id="user_id" class="form-control{{ $errors->has('user_id') ? ' is-invalid' : '' }}" name="user_id" value="{{ old('user_id') }}">
                                @if ($errors->has('user_id'))
                                    <span class="invalid-feedback"><strong>{{ $errors->first('user_id') }}</strong></span>
                                @endif
                            </div>
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" name="pay" id="pay" @if(old('pay')) checked @endif value="1">
                                <label class="custom-control-label" for="pay">User already has successfully shifts. Do you want to pay to inviter?</label>
                            </div>

                            <div class="form-group text-center">
                                <button type="submit" @click="$loading.show()" class="btn btn-primary">Save</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
