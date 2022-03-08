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
        hr {
            margin: 33px 0;
            background-color: #d6eaf5;
            border: none;
            height: 1px;
        }
        h1 {
            color: #000;
        }

        .subtitle {
            font-size: 16px;
            font-weight: bold;
        }
        .stars {
            font-size: 30px;
            color: #e3eaee;
        }
        .photo {
            border-radius: 50%;
            max-width: 65px;
            margin-top: 20px;
        }
    </style>
@endsection

@section('content')
    <div class="content">
        <img class="photo" src="{{ $photo }}">
        <h1>Thank you for working with {{ $name }}</h1>
        <p class="subtitle">{{ $date }}</p>
        <hr>
        <p class="subtitle">Your feedback is important! Please take a moment to rate</p>
        <div class="stars">
            <i class="fa fa-star" aria-hidden="true"></i>
            <i class="fa fa-star" aria-hidden="true"></i>
            <i class="fa fa-star" aria-hidden="true"></i>
            <i class="fa fa-star" aria-hidden="true"></i>
            <i class="fa fa-star" aria-hidden="true"></i>
        </div>

        <a class="button-link" href="{{ $link }}">GIVE FEEDBACK</a>

        <p>Every time you rate your experience, we can better match you in the future. Plus, your feedback goes to helping us make sure that Boon is a safe platform for everyone.</p>
    </div>
@endsection