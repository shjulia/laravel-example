@extends('emails.layouts.main')

@section('assets')
    <style>
        .blue-block {
            background-color: #f4f7f9;
            padding: 24px;
        }
        .plitt {
            font-size: 12px;
        }
        .codeblock {
            background-color: #d6eaf5;
            border: 1px dashed #3e9ecf;
            color: #3e9ecf;
            margin: 10px auto;
            width: 138px;
            padding: 6px 36px;
            font-weight: bold;
        }
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
        hr {
            margin: 33px 0;
            background-color: #d6eaf5;
            border: none;
            height: 1px;
        }
        .subtitle {
            font-size: 15px;
        }
        .width-47 {
            width: 47px;
        }
        p {
            font-size: 14px;
            line-height: 20px;
        }
        .link {
            text-decoration: underline;
            color: #3398cc;
        }
        .small {
            font-size: 13px;
            line-height: 20px;
            margin-top: 0;
        }
    </style>
@endsection

@section('content')
    <div class="content text-center">
        <img style="width: 76px;" src="https://boonb.s3.amazonaws.com/prod/img/notes.png">
        <h1>Request Temps Confidently via Boon</h1>
        <p class="subtitle">
            Finding a temp can be hard, especially one that has been verified, background checked and is held to a high standard of care. Requesting via Boon can give you peace of mind.
        </p>
        <hr>

        <h2>DID YOU KNOW?</h2>
        <img class="width-47" src="https://boonb.s3.amazonaws.com/prod/img/question.png">
        <p class="subtitle">31% of Temps Have a Questionable Past</p>
        <p>
            Boon takes security of your Practice and Patients very seriously and we allow only the very best Providers on our platform.
            <br>
            <span class="small">
                In fact, Out of all the applicants via Boon, <b>31% come back with marks on the background check, expired professional licenses or other disqualifying factors</b>.
            </span>
        </p>

        <img class="width-47" src="https://boonb.s3.amazonaws.com/prod/img/shield.png">
        <p class="subtitle">Make sure you bring in verified temps to your practices.</p>
        <p>
            Boon gives you the confidence that the temps you are requesting have active licenses and are background checked and verified, not just once but on an ongoing basis for the safety of your practice and the safety of your patients.
        </p>

        <a href="{{ $link }}" class="link">Request a Temp Today</a>

        <hr>

        <p>Have questions about requesting providers via Boon? Feel free to contact us or browse some of our frequently asked questions.</p>
        <p><a class="button-link" href=" https://www.doingboon.com/faq/">FAQS</a></p>
    </div>
@endsection
