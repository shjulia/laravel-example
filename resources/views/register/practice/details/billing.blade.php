@extends('layouts.main')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <a href="{{ route('practice.details.team') }}" class="back" @click="$loading.show()"><i class="fa fa-chevron-left"></i> BACK</a>
                        <h2 class="detailsh2">Billing Details</h2>
                        <billing
                            action="{{ route('practice.details.billingSave') }}"
                            pk_stripe="{{ env('STRIPE_PUBLIC_KEY') }}"
                            v-cloak
                        ></billing>
                        @if($user->practice->stripe_client_id)
                            <div class="form-group text-center">
                                <p>You already set up billing details, you can skip this step</p>
                                <a class="btn form-button skip-button" @click="$loading.show()" href="{{ route('practice.details.success') }}">Skip</a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('custom-scripts')
    <script src="https://js.stripe.com/v3/"></script>
@endpush
