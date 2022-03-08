@extends('emails.layouts.main')

@section('assets')
    <style>
        h2 {
            font-weight: bold;
        }
        .ptitle {
            color: #3397cc;
            font-size: 17px;
            font-weight: bold;
        }
    </style>
@endsection

@section('content')
    <div class="content text-center">
        <img style="width: 63px;" src="https://boonb.s3.amazonaws.com/prod/img/clock-email.png">
        <h2>Congratulations!</h2>
        <p class="ptitle">You have worked more than 40 hours via Boon!</p>
        <p>As a little thank you, we are adding an additional $25 to your account!</p>
        <img src="https://boonb.s3.amazonaws.com/prod/img/congrat.png">
        <p>Keep Practicing Good!</p>
        <img src="https://boonb.s3.amazonaws.com/prod/img/practicing-good.png">
        <p>
            <a href="{{ route('home') }}">{{ route('home') }}</a>
        </p>
    </div>
@endsection
