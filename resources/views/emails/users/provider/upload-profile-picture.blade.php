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
            max-width: 260px;
            margin: 40px auto;
            text-decoration: none;
            color: #000 !important;
        }
        .top-img {
            width: 76px;
        }
        .size-74-72 {
            width: 74px;
            height: 72px;
            padding-right: 25px;
        }
        .table {
            border: none;
            width: 80%;
            margin: auto;
        }
        .table h3 {
            font-size: 16px;
            text-align: left;
            margin: 0;
            font-weight: bold;
        }
        .table p {
            font-size: 14px;
            text-align: left;
            margin: 0 0 20px;
        }
        hr {
            margin: 33px 0;
            background-color: #d6eaf5;
            border: none;
            height: 1px;
        }
        h1 {
            color: #000;
        }
        .subtitle {
            font-size: 15px;
            line-height: 20px;
        }
    </style>
@endsection

@section('content')
    <div class="content">
        <img class="top-img" src="https://s3.amazonaws.com/boonb/prod/img/profile-top.png">
        <h1>Looks like we are having trouble viewing your profile picture.</h1>
        <p class="subtitle">
            An accurate profile picture is important so the Practice can verify your identify when you arrive. Safety is a top priority and having a clear photo can help in fulfilling our mission of practicing good.        </p>
        <hr>

        <table class="table">
            <tr>
                <td>
                    <img class="size-74-72" src="https://s3.amazonaws.com/boonb/prod/img/profile.png">
                </td>
                <td>
                    <h3>Make sure you are looking directly at the camera.</h3>
                    <p>
                        While staring off into the distance makes a wonderful glamour shot, we really just need the practice to know what you look like.
                    </p>
                </td>
            </tr>
            <tr>
                <td>
                    <img class="size-74-72" src="https://s3.amazonaws.com/boonb/prod/img/smile.png">
                </td>
                <td>
                    <h3>We need to see just you.</h3>
                    <p>
                        We love your friends and family and your cute little pet, but for this picture, we just want to see your smiling face.
                    </p>
                </td>
            </tr>
            <tr>
                <td>
                    <img class="size-74-72" src="https://s3.amazonaws.com/boonb/prod/img/focus.png">
                </td>
                <td>
                    <h3>Keep it in focus and authentic.</h3>
                    <p>
                        That soft glow filter is nice and so the hearts floating around your head, but we need an authentic, in-focus picture without the effects for this picture.                    </p>
                </td>
            </tr>
        </table>

        <hr>

        <a class="button-link" href="{{ $link }}">UPLOAD NEW PROFILE PICTURE HERE</a>
    </div>
@endsection