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
        .title {
            color: #3398CC;
            font-weight: 100;
        }
        .float-right {
            float: right;
        }
        .width-64 {
            width: 64px;
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
    </style>
@endsection

@section('content')
    <div class="content">
        <img class="width-64" src="https://s3.amazonaws.com/boonb/prod/img/check.png">
        <h1>Congrats on Requesting Your first Provider</h1>
        <h3>Here are some things to know!</h3>

        <hr>
        <table class="table">
            <tr>
                <td>
                    <img class="size-74-72" src="https://s3.amazonaws.com/boonb/prod/img/matching-begins.png">
                </td>
                <td>
                    <h3>Matching Begins</h3>
                    <p>
                        As soon as you request a Provider, the platform begins matching immediately. It takes skills requested, Provider experience, Practice location and many other factors to find the ideal temp for you.
                    </p>
                </td>
            </tr>
            <tr>
                <td>
                    <img class="size-74-72" src="https://s3.amazonaws.com/boonb/prod/img/safety.png">
                </td>
                <td>
                    <h3>Safety</h3>
                    <p>
                        Boon conducts background checks and license verification on all applicable Providers. This is to ensure safety of your Practice and your Patients. Additionally, we use Government IDs and facial recognition to further ensure safety.
                    </p>
                </td>
            </tr>
            <tr>
                <td>
                    <img class="size-74-72" src="https://s3.amazonaws.com/boonb/prod/img/cost-of-provider.png">
                </td>
                <td>
                    <h3>Cost of Provider</h3>
                    <p>
                        Boon operates in flat payments per shifts. The amount you were displayed is the total cost of the shift. No hidden fees. The cost per Provider may vary from time-to-time based on availability, experience and many other factors. All of Boon’s transaction costs are in the price displayed to you.
                    </p>
                </td>
            </tr>
            <tr>
                <td>
                    <img class="size-74-72" src="https://s3.amazonaws.com/boonb/prod/img/paying-the-provider.png">
                </td>
                <td>
                    <h3>Paying the Provider</h3>
                    <p>
                        You are directly hiring the Provider and are fully responsible for the Provider’s actions. Boon is a matching platform and payment facilitator meaning we help you find a Provider and will help transfer your funds to them (just like Paychex, ADP or another payroll service). However, the Provider is working directly for you.
                    </p>
                </td>
            </tr>
            <tr>
                <td>
                    <img class="size-74-72" src="https://s3.amazonaws.com/boonb/prod/img/so-shows.png">
                </td>
                <td>
                    <h3>No shows, cancellations, etc.</h3>
                    <p>
                        In the unlikely event of a provider not showing up for a shift, you can use the app to say “Provider Did Not Show Up.” At this point, you will be able to either request another provider or cancel the shift at no charge. If you choose to cancel a shift after a Provider has been matched, <b>a $50 cancellation fee may apply</b>.
                    </p>
                </td>
            </tr>
            <tr>
                <td>
                    <img class="size-74-72" src="https://s3.amazonaws.com/boonb/prod/img/feedback.png">
                </td>
                <td>
                    <h3>Feedback</h3>
                    <p>
                        Our platform is built on your feedback. Every time you leave feedback, we make our platform safer and better. Further, we are able to better match providers to your practice in the future.
                    </p>
                </td>
            </tr>
        </table>

        <hr>

        <p>If you have any questions, please don’t hesitate to reach out to us or view our FAQs here</p>

        <a class="button-link" href="{{ $link }}">FAQs</a>
    </div>
@endsection