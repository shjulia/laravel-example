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
    </style>
@endsection

@section('content')
    <div class="content">
        <img src="https://s3.amazonaws.com/boonb/prod/img/lock.png" alt="">
        <h2>It happens to the best of us</h2>
        <p>Changing your password is just a click away! Click the button below.</p>
        <a class="button-link" href="{{ route('password.reset', $token) }}">RESET MY PASSWORD</a>
    </div>
@endsection