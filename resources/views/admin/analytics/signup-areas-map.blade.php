@extends('layouts.main')

@section('content')
    <signups-areas-map
        :areas="{{ $areas }}"
    >

    </signups-areas-map>

@endsection
