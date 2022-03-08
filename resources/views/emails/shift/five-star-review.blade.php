@extends('emails.layouts.main')

@section('assets')
    <style>
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
            color: #1AF5A5;
        }
    </style>
@endsection

@section('content')
    <div class="content">
        <h1>You Are Awesome!</h1>
        <p class="subtitle">You just received a Five-Star Review</p>
        <hr>
        <div class="stars">
            <i class="fa fa-star" aria-hidden="true"></i>
            <i class="fa fa-star" aria-hidden="true"></i>
            <i class="fa fa-star" aria-hidden="true"></i>
            <i class="fa fa-star" aria-hidden="true"></i>
            <i class="fa fa-star" aria-hidden="true"></i>
        </div>
        <p class="subtitle">{{ $name }}, Just gave you five stars for being awesome on {{ $date }}</p>

        <p>Thank you for helping us by practicing good! That is what we are all about.</p>
    </div>
@endsection