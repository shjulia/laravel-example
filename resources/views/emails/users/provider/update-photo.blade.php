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
        .top-img {
            width: 264px;
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
            font-size: 14px;
            line-height: 20px;
        }
    </style>
@endsection

@section('content')
    <div class="content">
        <img class="top-img" src="https://s3.amazonaws.com/boonb/prod/img/id-card.png">
        <p class="subtitle">
            We are having a bit of trouble reading your Government-Issued ID. This is very important to the safety of everyone on Boon's platform. Could you retake a photo? Here are some best practices:
        </p>
        <hr>

        <table class="table">
            <tr>
                <td>
                    <img class="size-74-72" src="https://s3.amazonaws.com/boonb/prod/img/id-card-sm.png">
                </td>
                <td>
                    <p>
                        Make sure all four corners of your ID are displayed
                    </p>
                </td>
            </tr>
            <tr>
                <td>
                    <img class="size-74-72" src="https://s3.amazonaws.com/boonb/prod/img/no-flash.png">
                </td>
                <td>
                    <p>
                        Typically it is best to NOT use a flash as that can cause a glare
                    </p>
                </td>
            </tr>
            <tr>
                <td>
                    <img class="size-74-72" src="https://s3.amazonaws.com/boonb/prod/img/light.png">
                </td>
                <td>
                    <p>
                        Look for light reflections, sometimes this can wash out your beautiful photo or make one of the numbers hard to read
                    </p>
                </td>
            </tr>
            <tr>
                <td>
                    <img class="size-74-72" src="https://s3.amazonaws.com/boonb/prod/img/id-yellow.png">
                </td>
                <td>
                    <p>
                        Make sure you are photographing a Government Issued ID like a Driver's License or Passport.
                    </p>
                </td>
            </tr>
            <tr>
                <td>
                    <img class="size-74-72" src="https://s3.amazonaws.com/boonb/prod/img/id-gray.png">
                </td>
                <td>
                    <p>
                        Try to take the photo on a flat, solid background
                    </p>
                </td>
            </tr>
        </table>

        <hr>

        <a class="button-link" href="{{ $link }}">UPLOAD NEW ID PHOTO HERE</a>
    </div>
@endsection