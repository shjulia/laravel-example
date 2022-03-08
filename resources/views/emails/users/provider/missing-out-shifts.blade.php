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
            text-transform: uppercase;
        }
        hr {
            margin: 33px 0;
            background-color: #d6eaf5;
            border: none;
            height: 1px;
        }
        p.bold {
            font-weight: bold;
        }
    </style>
@endsection

@section('content')
    <div class="content text-center">
        <img style="width: 76px;" src="https://boonb.s3.amazonaws.com/prod/img/sad.png">
        <h2>$20.50 Could have Been Yours!</h2>
        <p class="bold">You’ve been invited to {{ $amount }} of shifts but we haven’t heard from you! That was over ${{ (int)round($sum, 0) }} in potential earnings.</p>
        <hr/>
        <p>Please be sure to update your account availability and Accept or Decline all shift invites.</p>
        <p class="bold">Update Your Availability</p>
        <p><a class="button-link" href="{{ route('account-details') }}">Go to Account Settings</a></p>
    </div>
@endsection
