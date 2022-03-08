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
    </style>
@endsection

@section('content')
    <div class="content">
        <img src="https://s3.amazonaws.com/boonb/prod/img/lock.png">
        <h2>Thank you for your interest in Boon. At this time, we are unable to approve your user account.</h2>
        <p>If you have any questions or if you think we've made a mistake, feel free to contact us.</p>
        <div style="text-align: right">
            <p>Best,</p>
            <p>Boon Team</p>
        </div>
    </div>
@endsection
