Thank you for working with {{ $practice->practice_name }}

Shift Details
{{ round($shift->shift_time / 60) }} hours
${{ $shift->cost_without_surge }}

@if($shift->surge_price)
    Bonus
    ${{ $shift->surge_price }}
@endif

How did things go?
Please take some time to provide additional feedback.
Review: {{ route('shifts.reviews.review', $shift->id) }}
