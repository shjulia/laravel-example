@extends('layouts.main')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card">
                    <div class="card-header">Coupon {{ $coupon->code }}</div>
                    <div class="card-body">
                        <div class="d-flex flex-row mb-3">
                            <a href="{{ route('admin.coupons.edit', $coupon) }}" class="btn btn-primary mr-1">Edit</a>

                            <form method="POST" action="{{ route('admin.coupons.destroy', $coupon) }}" class="mr-1">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-danger delete-button-alert">Delete</button>
                            </form>
                            <a href="{{ route('admin.coupons.index') }}" class="btn btn-success mr-1">List</a>
                        </div>
                        @if ($coupon->isExpired())
                            <div class="alert alert-danger" role="alert">
                                Coupon is expired
                            </div>
                        @endif

                        <table class="table table-bordered table-striped">
                            <tbody>
                            <tr>
                                <th>ID</th><td>{{ $coupon->id }}</td>
                            </tr>
                            <tr>
                                <th>Code</th><td>{{ $coupon->code }}</td>
                            </tr>
                            <tr>
                                <th>Date</th><td>{{ $coupon->start_date . ' - ' . $coupon->end_date }}</td>
                            </tr>
                            <tr>
                                <th>Coupon</th><td>
                                    @if ($coupon->dollar_off)
                                        -${{ $coupon->dollar_off }}
                                    @endif
                                    @if ($coupon->percent_off)
                                        -{{ $coupon->percent_off }}%
                                    @endif
                                </td>
                            </tr>
                            @if ($coupon->minimum_bill)
                                <tr>
                                    <th>Minimum bill</th><td>${{ $coupon->minimum_bill }}</td>
                                </tr>
                            @endif
                            @if ($coupon->use_per_account_limit)
                                <tr>
                                    <th>Per account limit</th><td>{{ $coupon->use_per_account_limit }}</td>
                                </tr>
                            @endif
                            @if ($coupon->use_globally_limit)
                                <tr>
                                    <th>Globally limit</th><td>{{ $coupon->use_globally_limit }}</td>
                                </tr>
                            @endif
                            <tr>
                                <th>States</th>
                                <td>
                                    @if (!$coupon->states)
                                        All
                                    @else
                                        @foreach($coupon->states as $state)
                                            {{ $state->title }};
                                        @endforeach
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Positions</th>
                                <td>
                                    @if (!$coupon->positions)
                                        All
                                    @else
                                        @foreach($coupon->positions as $position)
                                            {{ $position->title }};
                                        @endforeach
                                    @endif
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
