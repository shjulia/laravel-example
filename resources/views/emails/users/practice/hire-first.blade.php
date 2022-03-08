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
    </style>
@endsection

@section('content')
    <div class="content text-center">
        <img style="width: 76px;" src="https://boonb.s3.amazonaws.com/prod/img/tag.png">
        <h2>Here's 10% Off</h2>
        <p>It's time to hire your first Provider.</p>
        <div class="blue-block">
            <p>Your account is approved, but you haven’t yet hired your first provider! Here is a coupon code for your first match!</p>
            <p class="plitt">by using this code:</p>
            <div class="codeblock">
                {{ $code }}
            </div>
        </div>
        <hr/>
        <p>Have questions about requesting providers via Boon? Feel free to contact us or browse some of our frequently asked questions.</p>
        <p><a class="button-link" href=" https://www.doingboon.com/faq/">FAQS</a></p>
    </div>
@endsection
