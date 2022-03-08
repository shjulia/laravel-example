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
        .bonus {
            width: 86px;
            height: 86px;
            margin: auto;
            background-image: url('https://boonb.s3.amazonaws.com/prod/img/oval.png');
        }
        .bonus span {
            display: inline-block;
            color: #61f4a5;
            font-size: 28px;
            font-weight: bold;
            margin-top: 24px;
        }
    </style>
@endsection

@section('content')
    <div class="content text-center">
        <p><img style="width: 74px;" src="https://boonb.s3.amazonaws.com/prod/img/group.png"></p>
        <p><img src="https://boonb.s3.amazonaws.com/prod/img/selebr.png"></p>
        <hr/>
        <div class="bonus">
            <span>${{ $invite->bonus_value }}</span>
        </div>
        <h2>You've Got Cash!</h2>
        <p><b>Your friend {{ $invite->user->full_name }} is now officially practicing good with boon! </b></p>
        <p>You've made the world a better place. Invite more friends today!</p>
        <p>
            <a href="{{ route('referral.index') }}">{{ route('referral.index') }}</a>
        </p>
        <p>Help us all care for practices, providers and patients better by practicing good!</p>
        <p>Happy referring!</p>
    </div>
@endsection
