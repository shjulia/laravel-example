@extends('emails.layouts.main')

@section('assets')
    <style>
        .title {
            font-weight: 100;
        }
        ul {
            text-align: center;
            list-style-type: none;
        }
        li {
            margin: 10px;
            text-align: center;
            background-color: #3398CC;
            color: white;
            border-radius: 7px;
            padding: 7px;
            box-sizing: border-box;
            display:inline-block;
        }

        .continue {
            color: rgb(77, 76, 77);
            display: block;
            background: rgb(97, 245, 164);
            margin: 28px auto 5px;
            padding: 14px;
            font-weight: 600;
            text-align: center;
            vertical-align: middle;
            text-decoration: none;
            border-radius: 7px;
        }

        .continue:hover {
            text-decoration: none;
        }
    </style>
@endsection

@section('content')
    <div class="content">
        <img src="https://boonb.s3.amazonaws.com/prod/img/clock.png">
        <h1 class="title">You're almost ready to start practicing good with boon</h1>
        <h3>It looks like there are still a few more items we need to process your account.</h3>
        <p>
            We take the security very seriously. We handle all of your information with bank-level encryption.
            We simply require some of these items for the safety of patients and so that we can make sure you
            get paid :)
        </p>
        <br>
        <p>
            Please add the following information:
            <ul>
                @if($user->signup_step == 'provider:industry')
                    <li>Industry</li>
                    <li>Driver's license</li>
                    <li>Professional license(s)</li>
                    <li>Social Security Information</li>
                @elseif($user->signup_step == 'provider:identity')
                    <li>Driver's license</li>
                    <li>Professional license(s)</li>
                    <li>Social Security Information</li>
                @elseif($user->signup_step == 'provider:license')
                    <li>Professional license(s)</li>
                    <li>Social Security Information</li>
                @elseif($user->signup_step == 'provider:check')
                    <li>Social Security Information</li>
                @endif
            </ul>

        @if (Route::has('signup.' . explode(':', $user->signup_step)[1]))
            <a class="continue" href="{{ route('signup.' . explode(':', $user->signup_step)[1], $user->tmp_token) }}">Continue sign up</a>
        @endif
        </p>
    </div>
@endsection
