@extends('emails.layouts.main')

@section('assets')
    <style>
        body header {
            height: 70px;
        }
        body span {
            font-size: 18px;
        }
        .content {
            padding: 0 !important;
        }
        .first-block {
            text-align: center;
        }
        .photo {
            width: 50%;
            margin: auto;
            padding-top: 50px;
        }
        .photo img {
            width: 100%;
            max-width: 138px;
            border-radius: 100%;
        }
        .info {
            padding: 0 10px;
            font-size: 18px;
            text-align: center;
        }
        .info p {
            margin: 0;
        }
        .info h1 {
            margin-top: 0;
        }
        .shift-date {
            color: #737373;
            margin-bottom: 20px;
            display: block;
        }
        .name {
            font-size: 20px;
        }
        .money {
            font-size: 20px;
            text-align: right;
            width: 100%;
        }
        .separator {
            width: 100%;
            height: 1px;
            background-color: #e4e4e4;
        }
        .question {
            padding: 0px 50px 20px;
            text-align: left;
        }
        .question p {
            margin-bottom: 0;
        }
        .question a {
            color: #3398CC;
        }
        .question a:hover {
            color: #156cdc;
        }
        .round-left,
        .round-right {
            width: 20px;
            height: 41px;
        }
        .round-left {
            background-image: url('https://boonb.s3.amazonaws.com/prod/img/round-left.png');
        }
        .round-right {
            background-image: url('https://boonb.s3.amazonaws.com/prod/img/round-right.png');
        }
        .second-block {
            padding: 50px;
            text-align: left;
        }
        .second-block h1,
        .additional-block h1 {
            margin-top: 0;
            font-size: 26px;
        }
        .hours {
            width: 50%;
        }
        .card img {
            width: 20px;
        }
        .card span {
            font-size: 22px;
        }
        .details {
            display: flex;
        }
        .details h1 {
            width: 50%;
            margin: 0;
            line-height: 0.9;
            text-align: right;
        }
        .details::after  {
            border-bottom: 1px solid #cbcbcb;
        }
        .additional-block {
            padding: 30px;
            box-sizing: border-box;
            background-color: #fff;
            text-align: center;
            border-radius: 10px;
            width: 60%;
            margin: 20px auto;
        }
        .review-button {
            color: #3398CC !important;
            background-color: #E2EAED;
            padding: 15px 0;
            width: 100%;
            display: block;
            margin: auto;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 600;
            font-size: 18px;
        }
        @media screen and (max-width: 640px) {
            h1 {
                font-size: 22px;
            }
            .additional-block {
                padding: 10px;
                box-sizing: border-box;
                background-color: #fff;
                text-align: center;
                border-radius: 10px;
                width: 90%;
                margin: 20px auto;
            }
        }
    </style>
@endsection

@section('content')
    <div class="content">
        <div class="first-block">
            <div class="photo">
                <img src="{{ $practice->practice_photo_url }}">
            </div>
            <div class="">
                <p><b>Thank you for working with</b></p>
                <p class="name">{{ $practice->practice_name }}</p>
                <span class="shift-date">{{ $shift->period() }}</span>
            </div>
        </div>
        <div class="separator"></div>
        <div class="second-block">
            <h1>Shift Details</h1>
            <div class="details">
                <span class="hours">{{ round($shift->shift_time / 60, 2) }} hours</span>
                <span class="money">${{ $shift->cost_without_surge }}</span>
            </div>
            @if($shift->surge_price)
                <div class="separator"></div>
                <div class="details">
                    <span class="hours">Bonus</span>
                    <span class="money">${{ $shift->surge_price + $shift->bonus }}</span>
                </div>
            @endif
        </div>

        <div class="question">
            <p>Questions or concerns about the above payment amount or hours worked?</p>
            <a href="mailto:hello@doingboon.com">Click here for help.</a>
        </div>
    </div>
    <div class="additional-block">
        <h1>How did things go?</h1>
        <p>Please take some time to provide additional feedback.</p>
        <a href="{{ route('shifts.provider.reviews.review', $shift->id) }}" class="review-button">Review</a>
    </div>
@endsection
