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
        <h1 class="title">Hi</h1>
        <img src="https://s3.amazonaws.com/boonb/prod/img/plane.png">
        <h2>We would love to hear what you thought of {{ $coworkerName }}. Please rate them now!</h2>

        <p></p>

        <a class="button-link" href="{{ $link }}">Leave Review</a>
    </div>
@endsection