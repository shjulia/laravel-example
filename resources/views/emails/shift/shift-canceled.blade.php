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
        <h1 class="title">Hi {{ $shift->provider->user->first_name }},</h1>
        <img src="https://s3.amazonaws.com/boonb/prod/img/plane.png">

        <p>Unfortunately, the practice has canceled the shift on
            {{ $shift->period($shift->provider->user->tz ?? null) }}. We wanted to let you know as soon as possible and apologize for the inconvenience.
        </p>
        <p>
            Thank you for using Boon! We hope to match you with other local work opportunities in the near future.
        </p>
        <div style="text-align: right">
            <p>Best,</p>
            <p>Boon Team</p>
        </div>
    </div>
@endsection
