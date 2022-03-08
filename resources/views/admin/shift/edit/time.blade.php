@extends('layouts.main')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-7">
                <div class="card">
                    <div class="card-header">Shift {{ $shift->id }}</div>
                    <div class="card-body">
                        @include('shift._time', [
                            'action' => route('admin.shifts.edit.time', $shift),
                            'previousRoute' => route('admin.shifts.show', $shift),
                            'isAdmin' => true
                        ])
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
