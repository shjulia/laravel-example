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
    </style>
@endsection

@section('content')
    <div class="content">
        <img src="https://s3.amazonaws.com/boonb/prod/img/plane.png">
        <p>The hours for your shift @if ($shift->isHasProvider()) with {{ $shift->provider->full_name }} @endif on {{ $shift->datePeriod() }} have been updated to {{ $shift->period() }}. The new price for the shift is ${{ $shift->cost_for_practice }}.</p>
        @if ($oldCost['costForPractice'] < $shift->cost_for_practice)
            <p>The additional charge will be added to your credit card on file.</p>
        @elseif ($oldCost['costForPractice'] > $shift->cost_for_practice)
            <p>The difference in the new lower cost will be returned to your credit card</p>
        @endif
    </div>
@endsection
