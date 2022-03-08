@extends('layouts.main')

@section('content')
    <signups-map
        :practices="{{ $signups['practices'] }}"
        :providers="{{ $signups['providers'] }}"
        :days="{{ collect($days) }}"
    >
    </signups-map>

@endsection
