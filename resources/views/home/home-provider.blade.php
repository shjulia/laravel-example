@extends('layouts.main')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card card-null">
                    <div class="card-body">
                        @if($user->specialist->isWaiting())
                            <p class="pre-notice">You will be notified within 72 hours of your status with Boon and can start working</p>
                        @endif
                        <div class="approval-status row">
                            <div class="col-sm-2">
                                <img src="{{ $user->specialist->photo ? $user->specialist->photo_url : asset('img/person.png') }}">
                            </div>
                            <div class="col-sm-6">
                                <p class="name">{{ $user->first_name . ' ' . $user->last_name }}</p>
                                <p class="spec">{{ $user->specialist->industry->title }}</p>
                            </div>
                            <div class="col-sm-4">
                                <span class="app-status {{ $user->specialist->isWaiting() ? 'pending' : 'approved' }}">
                                    {{ $user->specialist->isWaiting() ? 'Pending' : 'Approved' }}
                                </span>
                            </div>
                        </div>
                        <div class="text-center">
                            @if ($user->specialist->isWaiting())
                                <h1 class="statush1">While You Wait...</h1>
                                <p class="notice">Your account is still being reviewed. Don't worry, this only takes 24-72 hours.</p>
                                @if (!$user->specialist->photo)
                                    <p class="notice">While you wait, please take a moment to help us get to know you and your availability better. This will help us better place you with the right opportunities to provide care.</p>
                                @endif
                            @else
                                <h1 class="statush1">Congratulations!</h1>
                                <p class="notice">You have been approved to be hired and start providing care.</p>
                                @if (!$user->specialist->photo)
                                    <p class="notice">In order to help us better place you with opportunities, please take a moment to get to know you and your availability better.</p>
                                @endif
                            @endif
                            @if (!$user->specialist->photo)
                                <a class="btn form-button" href="{{ route('account-details') }}">Setup your account</a>
                            @elseif (!$user->specialist->isSetTransferInfo())
                                <a class="btn form-button" href="{{ route('provider.edit.getPaid') }}" style="max-width: 210px;">Tell Us How to Pay You</a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="container referral">
        <div class="row justify-content-center">
            <div class="col-md-9">
                @if ($referral)
                    @include('referral._referral-info')
                @endif
            </div>
        </div>
    </div>
@endsection
