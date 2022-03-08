@extends('emails.layouts.main')

@section('assets')
    <style>
        body header {
            height: 70px;
        }
        body p {
            font-size: 18px;
        }
        .additional-block {
            padding: 30px;
            background-color: #fff;
            border-radius: 10px;
            display: block;
            text-align: center;
            width: 60%;
            max-width: 90%;
            margin: 30px auto;
            box-sizing: border-box;
        }
        .content img {
            max-width: 80%;
        }
        .review-button {
            color: #1F2022 !important;
            background-color: #1AF5A5;
            padding: 15px;
            max-width: 320px;
            display: block;
            margin: auto;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 600;
            font-size: 18px;
        }

        @media screen and (max-width: 640px) {
            .additional-block {
                padding: 10px;
                width: 90%;
                max-width: 90%;
                box-sizing: border-box;
            }
        }
    </style>
@endsection

@section('content')
    <div class="content">
        <h1>It Pays to Practice Good!</h1>
        <img src=" {{ asset('img/ref-friend.jpg') }}" alt="">
        <p>Congratulations, your referral {{ $referral_name }} just signed up for boon! Once they successfully complete
            their first shift, you'll have ${{ $referral_fee }} waiting for you.</p>
    </div>

    <div class="additional-block">
        <h1>What are you going to do with all that cash?</h1>
        <p>Hopefully make it rain and spread the word about practicing good with boon!</p>
        <a href="{{ route('referral.index') }}" class="review-button">REFER MORE FRIEND NOW</a>
    </div>
@endsection