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
        <img src="https://s3.amazonaws.com/boonb/prod/img/lock.png">
        <h2>Congratulations! Your profile has been reviewed and you are now approved to start practicing good with Boon.</h2>
        <p></p>
        <a class="button-link" href="{{ url('/') }}">View Profile</a>
    </div>
@endsection
