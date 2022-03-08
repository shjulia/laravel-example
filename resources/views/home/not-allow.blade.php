@extends('layouts.main')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card card-null">
                    <div class="card-body">
                        <div class="text-center not-allowed">
                            <div class="topblock">
                                <i class="fa fa-envelope-o" aria-hidden="true"></i>
                                <h1 class="statush1">Congratulations!</h1>
                                <p>You have successfully signed up for boon and you are one step closer to practicing good.</p>
                            </div>
                            <div class="bottomblock">
                                <i class="fa fa-user-o" aria-hidden="true"></i>
                                <h4> As of right now, boon is not yet available in {{ $city }}. </h4>
                                <p>
                                   But have no fear! We are working quickly to open your city and you’ll be the first to know. Your application is saved and you’ll be all set when boon comes to your area.
                                </p>
                                <p>
                                    In the meantime, help us open {{ $city }} sooner by referring other Practices and Providers. Plus, earn cash while doing it.
                                </p>
                                @if ($user->referral)
                                    <p>You can invite people by link: <b>{{ route('signup.userBaseByInvite', ['code' => $user->referral->referral_code]) }}</b></p>
                                    <a class="btn form-button" href="{{ route('referral.index') }}">Referral program</a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
