@extends('emails.layouts.main')

@section('assets')
    <style>
        h2 {
            font-weight: bold;
        }
        hr {
            margin: 33px 0;
            background-color: #d6eaf5;
            border: none;
            height: 1px;
        }
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
    <div class="content text-center">
        <img style="width: 91px;" src="https://boonb.s3.amazonaws.com/prod/img/dollars.png">
        <h2>You drove {{ $distance }} miles to your most recent shift!</h2>
        <p>You deserve a double high five and double Lincolns because you're a road warrior.</p>
        <p><b>We've added ${{ $bonus }} to your account.</b></p>
        <hr>
        <p>
            <a class="button-link" href="{{ route('shifts.provider.index') }}">GO TO BOON WALLET</a>
        </p>
    </div>
@endsection
