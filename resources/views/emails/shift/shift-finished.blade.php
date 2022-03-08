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
            padding: 30px;
            display: flex;
        }
        .photo {
            width: 50%;
            padding: 0 10px;
        }
        .photo img {
            width: 100%;
            max-width: 138px;
            border-radius: 100%;
        }
        .info {
            width: 80%;
            padding: 0 10px;
            font-size: 18px;
            text-align: left;
        }
        .info p {
            margin: 0;
        }
        .info h1 {
            margin-top: 0;
        }
        .info .date {
            color: #3398CC;
        }
        .separator {
            display: flex;
            width: 100%;
        }
        hr {
            border:none;
            border-top: 2px dashed #B4B4B4;
            position: relative;
            width: 93%;
            margin: auto;
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
                <img src="{{ $provider->specialist->photo_url }}">
            </div>
            <div class="info">
                <span class="date">{{ date('F j, Y \\a\t h:i A', strtotime($shift->end_date . ' ' . $shift->from_time)) }}</span>
                <p>Thank you for working with</p>
                <h1>{{ $provider->first_name . ' ' . $provider->last_name }}</h1>
            </div>
        </div>
        <div class="separator">
            <div class="round-left"></div>
            <hr>
            <div class="round-right"></div>
        </div>
        <div class="second-block">
            <h1>Shift Details</h1>
            <div class="details">
                <span class="hours">{{ round($shift->shift_time / 60) }} hours</span>
                <h1>${{ $shift->cost }}</h1>
            </div>
        </div>
    </div>
    <div class="additional-block">
        <h1>How did things go?</h1>
        <p>Please take some time to provide additional feedback.</p>
        <a href="{{ route('shifts.reviews.review', $shift->id) }}" class="review-button">Review</a>
    </div>
@endsection