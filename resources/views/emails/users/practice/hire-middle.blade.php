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
        <h2>Here's 50% Off</h2>
        <p>That’s right, we are willing to cover 50% of the cost to bring in your next temp. What do you think? It's time to hire your first Provider.</p>
        <div class="blue-block">
            <p><b>It’s probably safe to assume that you’ve never had someone offer to cover half of the cost of a temp for the day. Well, we are so confident you will love requesting a background checked, verified and accountable Provider via Boon that we want to give you a huge discount.</b></p>
            <p class="plitt">by using this code:</p>
            <div class="codeblock">
                {{ $code }}
            </div>
            <p>The code above is valid for your Practice only. It is good for one shift on any Provider and can be used anytime in the next 14 days.</p>
        </div>
        <hr/>
        <p>Have questions about requesting providers via Boon? Feel free to contact us or browse some of our frequently asked questions.</p>
        <p><a class="button-link" href=" https://www.doingboon.com/faq/">FAQS</a></p>
    </div>
@endsection
