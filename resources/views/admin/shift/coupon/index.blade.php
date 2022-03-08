@extends('layouts.main')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card">
                    <div class="card-header">Coupons</div>
                    <div class="card-body">
                        <ul class="nav nav-tabs" id="myTab" role="tablist">
                            <li class="nav-item {{ $type == 'custom'  ? 'active' : '' }}">
                                <a class="nav-link {{ $type == 'custom'  ? 'active show' : '' }}" id="custom-tab"  href="{{ route('admin.coupons.index') }}" aria-selected="true">Custom</a>
                            </li>
                            <li class="nav-item {{ $type == 'auto'  ? 'active' : '' }}">
                                <a class="nav-link {{ $type == 'auto'  ? 'active show' : '' }}" id="auto-tab" href="{{ route('admin.coupons.auto') }}" >Auto</a>
                            </li>
                        </ul>
                        <p class="mt-2"></p>
                        @if ($type == "custom")
                            <a href="{{ route('admin.coupons.create') }}" class="btn btn-success mb-2">Add Coupon</a>
                        @endif
                        <table class="table table-bordered table-striped">
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th>Code</th>
                                <th>Start Date</th>
                                <th>End Date</th>
                                <th>Coupon</th>
                                @if ($type == "auto")
                                    <th>Practice</th>
                                @endif
                            </tr>
                            </thead>
                            <tbody>

                            @foreach ($coupons as $coupon)
                                <tr>
                                    <td><a href="{{ route('admin.coupons.show', $coupon) }}">{{ $coupon->id }}</a></td>
                                    <td>{{ $coupon->code }}</td>
                                    <td>{{ $coupon->start_date }}</td>
                                    <td>{{ $coupon->end_date }}</td>
                                    <td>
                                        @if ($coupon->dollar_off)
                                            -${{ $coupon->dollar_off }}
                                        @endif
                                        @if ($coupon->percent_off)
                                            -{{ $coupon->percent_off }}%
                                        @endif
                                    </td>
                                    @if ($type == "auto")
                                        <td>
                                            @if ($coupon->practice_id)
                                                {{ $coupon->practice->practice_name }}
                                            @endif
                                        </td>
                                    @endif
                                </tr>
                            @endforeach

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
