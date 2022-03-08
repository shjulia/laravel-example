@extends('layouts.main')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card">
                    <div class="card-header">Edit coupon {{ $coupon->code }}</div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('admin.coupons.update', $coupon) }}">
                            @method('PUT')
                            @csrf

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="start_date" class="col-form-label">Start Date</label>
                                        <input id="start_date" type="date" class="form-control{{ $errors->has('start_date') ? ' is-invalid' : '' }}" name="start_date" value="{{ old('start_date', $coupon->start_date) }}" required>
                                        @if ($errors->has('start_date'))
                                            <span class="invalid-feedback"><strong>{{ $errors->first('start_date') }}</strong></span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="end_date" class="col-form-label">End Date</label>
                                        <input id="end_date" type="date" class="form-control{{ $errors->has('end_date') ? ' is-invalid' : '' }}" name="end_date" value="{{ old('end_date', $coupon->end_date) }}" required>
                                        @if ($errors->has('end_date'))
                                            <span class="invalid-feedback"><strong>{{ $errors->first('end_date') }}</strong></span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="percent_off" class="col-form-label">Percent off</label>
                                        <input id="percent_off" type="text" class="form-control{{ $errors->has('percent_off') ? ' is-invalid' : '' }}" name="percent_off" value="{{ old('percent_off', $coupon->percent_off) }}">
                                        @if ($errors->has('percent_off'))
                                            <span class="invalid-feedback"><strong>{{ $errors->first('percent_off') }}</strong></span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="dollar_off" class="col-form-label">Dollar off</label>
                                        <input id="dollar_off" type="text" class="form-control{{ $errors->has('dollar_off') ? ' is-invalid' : '' }}" name="dollar_off" value="{{ old('dollar_off', $coupon->dollar_off) }}">
                                        @if ($errors->has('dollar_off'))
                                            <span class="invalid-feedback"><strong>{{ $errors->first('dollar_off') }}</strong></span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="minimum_bill" class="col-form-label">Minimum bill</label>
                                        <input id="minimum_bill" type="text" class="form-control{{ $errors->has('minimum_bill') ? ' is-invalid' : '' }}" name="minimum_bill" value="{{ old('minimum_bill', $coupon->minimum_bill) }}">
                                        @if ($errors->has('minimum_bill'))
                                            <span class="invalid-feedback"><strong>{{ $errors->first('minimum_bill') }}</strong></span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="use_per_account_limit" class="col-form-label">Per account limit</label>
                                        <input id="use_per_account_limit" type="number" class="form-control{{ $errors->has('use_per_account_limit') ? ' is-invalid' : '' }}" name="use_per_account_limit" value="{{ old('use_per_account_limit', $coupon->use_per_account_limit) }}">
                                        @if ($errors->has('use_per_account_limit'))
                                            <span class="invalid-feedback"><strong>{{ $errors->first('use_per_account_limit') }}</strong></span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="use_globally_limit" class="col-form-label">Globally limit</label>
                                        <input id="use_globally_limit" type="number" class="form-control{{ $errors->has('use_globally_limit') ? ' is-invalid' : '' }}" name="use_globally_limit" value="{{ old('use_globally_limit', $coupon->use_globally_limit) }}">
                                        @if ($errors->has('use_globally_limit'))
                                            <span class="invalid-feedback"><strong>{{ $errors->first('use_globally_limit') }}</strong></span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="position">Positions</label>
                                        <select
                                            id="position"
                                            class="select2m form-control{{ $errors->has('position') ? ' is-invalid' : '' }}"
                                            name="position[]"
                                            multiple
                                            v-select2
                                        >
                                            @foreach ($positions as $position)
                                                @if($position->children->isEmpty())

                                                    <option
                                                        value="{{ $position->id }}"
                                                        @if(in_array($position->id, old('position', $coupon->positions->pluck('id')->toArray()))) selected @endif
                                                    >
                                                        {{ $position->title }}
                                                    </option>
                                                @else
                                                    <optgroup label="{{ $position->title }}">
                                                        @foreach ($position->children as $child)
                                                            <option
                                                                value="{{ $child->id }}"
                                                                @if(in_array($child->id, old('position', $coupon->positions->pluck('id')->toArray()))) selected @endif
                                                            >
                                                                {{ $child->title }}
                                                            </option>
                                                        @endforeach
                                                    </optgroup>
                                                @endif
                                            @endforeach
                                        </select>
                                        @if ($errors->has('position'))
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $errors->first('position') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="state">States</label>
                                        <select
                                            id="state"
                                            class="select2m form-control{{ $errors->has('state') ? ' is-invalid' : '' }}"
                                            name="state[]"
                                            multiple
                                            v-select2
                                        >
                                            @foreach ($states as $state)
                                                <option @if(in_array($state->id, old('state', $coupon->states->pluck('id')->toArray()))) selected @endif value="{{ $state->id }}">{{ $state->title }}</option>
                                            @endforeach
                                        </select>
                                        @if ($errors->has('state'))
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $errors->first('state') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>
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
