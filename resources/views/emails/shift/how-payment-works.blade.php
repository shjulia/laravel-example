@extends('emails.layouts.main')

@section('assets')
    <style>

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

        .subtitle {
            font-size: 15px;
        }
    </style>
@endsection

@section('content')
    <div class="content">
        <img class="width-64" src="https://s3.amazonaws.com/boonb/prod/img/extra-cash.png">
        <h1>You’re on your way to earning extra cash!</h1>
        <p class="subtitle">Working via Boon makes it simple for you to pick up extra shifts. We want to make sure you get paid for your shift so here are some things to know</p>

        <hr>
        <table class="table">
            <tr>
                <td>
                    <img class="size-74-72" src="https://s3.amazonaws.com/boonb/prod/img/how-paid.png">
                </td>
                <td>
                    <h3>How You’ll Be Paid</h3>
                    <p>
                        You will be paid via ACH directly to your bank account. Be sure your bank details and payment preferences are up-to-date <a href="{{ $link }}">here</a>.
                    </p>
                </td>
            </tr>
            <tr>
                <td>
                    <img class="size-74-72" src="https://s3.amazonaws.com/boonb/prod/img/time-keeping.png">
                </td>
                <td>
                    <h3>Time Keeping</h3>
                    <p>
                        Boon is based on day-rate pay, not hourly pay. You will be paid the flat rate for the shift you accepted. At the end of the shift, if there was a significant overage, you can request overtime pay. Now there is no more need to punch a clock.
                    </p>
                </td>
            </tr>
            <tr>
                <td>
                    <img class="size-74-72" src="https://s3.amazonaws.com/boonb/prod/img/who-pay.png">
                </td>
                <td>
                    <h3>Who is Paying You</h3>
                    <p>
                        Boon is a matching platform and payment facilitator. We collect the Practice’s funds and store them in your Boon account until they are disbursed to you. This workflow helps to makes sure you will be paid every time and on time. Never request money from the Practice directly.
                    </p>
                </td>
            </tr>
            <tr>
                <td>
                    <img class="size-74-72" src="https://s3.amazonaws.com/boonb/prod/img/taxes.png">
                </td>
                <td>
                    <h3>Taxes</h3>
                    <p>
                        You are independently working directly for the Practice, not for Boon. Boon just facilitates the payment like a payroll company would. While you will need to consult your own tax advisor, most workers on Boon will need to withhold their own taxes.
                    </p>
                </td>
            </tr>
            <tr>
                <td>
                    <img class="size-74-72" src="https://s3.amazonaws.com/boonb/prod/img/payment-confirmation.png">
                </td>
                <td>
                    <h3>Payment Confirmation</h3>
                    <p>
                        At the completion of your shift, you will receive a confirmation email from Boon similar to a pay stub. Keep this for your records. Should there ever be any discrepancy, let us know and we will work to make it right.
                    </p>
                </td>
            </tr>
        </table>

        <hr>

        <p>Thank you for working via Boon!</p>
    </div>
@endsection