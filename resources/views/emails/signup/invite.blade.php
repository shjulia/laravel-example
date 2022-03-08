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
        <h2>You have been invited to Boon by {{ $referralFullName }}</h2>
        <p>What is Boon?</p>
        <p>
            We match qualified dental providers with temporary work opportunities at local practices. <a href="https://www.doingboon.com/">Learn more.</a>
        </p>

        <a class="button-link" href="{{ route('signup.userBaseByInvite', ['code' => $referralCode]) }}">Accept Invitation</a>
    </div>
@endsection
