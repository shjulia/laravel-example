@extends('emails.layouts.main')

@section('assets')
    <style>
        .button-link {
            border: 0;
            background-color: #1AF5A5;
            border-radius: 8px;
            padding: 20px 40px;
            font-weight: 600;
            display: block;
            max-width: 250px;
            margin: 40px auto;
            text-decoration: none;
            color: #000 !important;
        }
        .title {
            color: #3398CC;
            font-weight: 100;
        }
    </style>
@endsection

@section('content')
    <div class="content">
        <img src="https://s3.amazonaws.com/boonb/prod/img/plane.png">
        <h2>Hi, {{ $user->first_name }}</h2>

        <p>You can earn $100 for every single provider or practice you refer to Boon that is successfully matched.</p>
        <p>Below is your unique referral link. Copy it and share it on social media, email it around and earn $100 for every provider or practice that signs up and is successfully matched. There is NO LIMIT to how many people you can refer.</p>

        <a href="{{ route('signup.userBaseByInvite', ['code' => $user->referral->referral_code]) }}">{{ route('signup.userBaseByInvite', ['code' => $user->referral->referral_code]) }}</a>

        <a class="button-link" href="{{ route('referral.index') }}">Referral Dashboard</a>
        <p style="font-size:12px">
            There is no limit to how many people you can invite. They must use your unique link (above). In order to earn the $100, the provider or practice must be successfully matched and have completed a shift.
        </p>
    </div>
@endsection
