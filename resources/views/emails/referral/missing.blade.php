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
    </style>
@endsection

@section('content')
    <div class="content text-center">
        <h2>Your friends are starting to sign-up</h2>
        <p><b>Let's make sure you earn your $100 referral fee.</b></p>
        <p><img style="width: 230px;" src="https://boonb.s3.amazonaws.com/prod/img/100referr.png"></p>
        <p>It looks like people have been clicking your referral link and starting to sign-up. However, they have to finish setting up their account and be matched to shift for you to earn your $100 referral bonus.</p>
        <hr/>
        <p>Follow-up with your friends now and help them finish their sign-up and get matched so you can earn your $100</p>
        @foreach($invites as $invite)
            <p><b>{{ $invite->user->full_name }}</b></p>
        @endforeach
    </div>
@endsection
