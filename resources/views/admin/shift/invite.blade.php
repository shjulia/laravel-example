@extends('layouts.main')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-3">
                <div class="card">
                    <div class="card-header">Shift {{ $shift->id }}</div>
                    <div class="card-body">
                        <shift-invite
                            check-url="{{ route('admin.shifts.inviteCheck', $shift) }}"
                            inline-template
                        >
                            <div>
                                <form action="{{ route('admin.shifts.invite', $shift) }}" method="POST" ref="form">
                                    @csrf
                                    <div class="form-group">
                                        <label for="provider_id" class="col-form-label">Provider id</label>
                                        <input id="provider_id" class="form-control{{ $errors->has('provider_id') ? ' is-invalid' : '' }}" v-model="providerId" name="provider_id" value="{{ old('provider_id') }}" required>
                                        @if ($errors->has('provider_id'))
                                            <span class="invalid-feedback"><strong>{{ $errors->first('provider_id') }}</strong></span>
                                        @endif
                                    </div>

                                    <div class="form-group text-center">
                                        <button type="button" class="btn btn-primary" @click="invite()">Invite</button>
                                    </div>
                                </form>
                            </div>
                        </shift-invite>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
