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
        <h1 class="title">Welcome to boon</h1>
        <img src="https://s3.amazonaws.com/boonb/prod/img/plane.png">
        <h2>Let's start practicing good together!</h2>

        <p>You've officially created an account. Click below to activate your account,
            choose a super secure password and finish your account set-up.</p>

        <a class="button-link" href="{{ $setPasswordLink }}">ACTIVATE YOUR ACCOUNT</a>
    </div>
@endsection