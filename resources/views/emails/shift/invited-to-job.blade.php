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
    </style>
@endsection

@section('content')
    <div class="content">
        <img src="https://s3.amazonaws.com/boonb/prod/img/plane.png">
        <h2>You have been invited to job</h2>
        <p>Hi {{ $name }},</p>
        <p>
            You've been matched for a multi-day shift with Boon! A practice in {{ $shift->practice_location->city . ' ' . $shift->practice_location->state }} needs a {{ $shift->position->title }} on {{ $shift->period() }} {{ $lunchText }}
            @if (!$shift->multi_days)
                and will pay you ${{ $shift->cost_without_surge }}.
                @if ($shift->bonuses)
                    This shift qualifies for a ${{ $shift->bonuses }} bonus which will be added to the pay mentioned above.
                @endif
            @endif
        </p>
        <p>Please note this shift is time sensitive and will be filled on a first come, first served basis.
            @if (!$shift->multi_days)
                Click below to learn more and accept the shift.
            @else
                Click to respond to work in part it in whole
            @endif
        </p>
        <a class="button-link" href="{{ route('shifts.provider.acceptPage', ['shift' => $shift]) }}">View Details</a>
        <div>
            <p>Best,</p>
            <p>Boon Team</p>
        </div>
    </div>
@endsection
