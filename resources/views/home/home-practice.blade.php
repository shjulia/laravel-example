@extends('layouts.main')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card card-null">
                    <div class="card-body">
                        <div class="approval-status row">
                            <div class="col-sm-2">
                                <img src="{{ $user->practice->practice_photo ? $user->practice->practice_photo_url : asset('img/person.png') }}">
                            </div>
                            <div class="col-sm-6">
                                <p class="name">{{ $user->practice->practice_name }}</p>
                                <p class="spec">{{ $user->practice->industry->title }}</p>
                            </div>
                            <div class="col-sm-4">
                                <span class="app-status {{  $user->practice->isWaiting() ? 'pending' : 'approved' }}">{{ $user->practice->isWaiting() ? 'Pending' : 'Approved' }}</span>
                            </div>
                        </div>
                            <div class="text-center">
                                @if ($user->practice->isWaiting())
                                    <h1 class="statush1">While You Wait...</h1>
                                    <p class="notice">Your account is still being reviewed. Don't worry, this only takes 24-72 hours.</p>
                                    @if (!$user->practice->isSetPaymentInfo())
                                        <p class="notice">In order to help us better connect you with providers, please take a moment to help us get to know you and your practice.</p>
                                    @endif
                                @else
                                    <h1 class="statush1">Congratulations!</h1>
                                    <p class="notice">You have been approved @if (!$user->practice->isSetPaymentInfo()) and can start hiring providers.@endif</p>
                                    @if (!$user->practice->isSetPaymentInfo())
                                       <p class="notice">In order to help us better connect you with providers, please take a moment to help us get to know you and your practice.</p>
                                    @endif
                                @endif
                                @if (!$user->practice->isSetPaymentInfo())
                                    <p class="notice">You should set payment info to start hiring providers.</p>
                                    <a class="btn form-button" href="{{ route('practice.details.base') }}">Setup practice account</a>
                                @endif
                            </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
