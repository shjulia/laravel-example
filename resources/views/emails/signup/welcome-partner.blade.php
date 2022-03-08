@extends('emails.layouts.main')

@section('assets')
    <style>
        .sub-content {
            padding: 30px;
            background-color: #fff;
            border-radius: 10px;
            text-align: center;
            width: 75%;
            max-width: 90%;
            margin: 30px auto;
        }
        .title, .subtitle {
            font-weight: 600;
            margin: 10px;
        }
        .cover {
            width: 230px;
        }
    </style>
@endsection

@section('content')
    <div class="content">
        <img class="cover" src="https://s3.amazonaws.com/boonb/prod/img/congrats_partner_signup.jpg">
        <h1 class="title">Welcome</h1>
        <h2 class="subtitle">Thanks for practicing good.</h2>
        <p>Enjoy the flexibility of being own boss and creating your own schedule while earning fair pay and providing
        high-quality dental care that focuses on patients</p>
        <a href="{{ $link }}">{{ $link }}</a>
    </div>
    <div class="sub-content">
        <p>Help us all care for practices, providers and patients better by practicing good!</p>
        <p>Happy referring!</p>
    </div>
@endsection