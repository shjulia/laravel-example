@extends('layouts.main')

@section('content')
    <div class="hire hire-center">
        @include('shift._time', [
            'action' => route('shifts.setTime', $shift),
            'isAdmin' => false
        ])
    </div>
@endsection
