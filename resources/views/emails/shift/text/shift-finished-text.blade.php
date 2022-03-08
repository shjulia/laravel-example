Thank you for working with {{ $provider->first_name . ' ' . $provider->last_name }}

Shift Details
{{ round($shift->shift_time / 60) }} hours
${{ $shift->cost }}

How did things go?
Please take some time to provide additional feedback.
Review: {{ route('shifts.reviews.review', $shift->id) }}
