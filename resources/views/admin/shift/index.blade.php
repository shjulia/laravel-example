@extends('layouts.main')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card">
                    <div class="card-header">Shifts</div>
                    <div class="card-body">
                        <p>
                            <a href="{{ isset($archived) ? route('admin.shifts.index') : route('admin.shifts.archived') }}" class="btn btn-primary btn-sm">{{ isset($archived) ? 'Real list' : 'Archived list' }}</a>
                        </p>
                        <table class="table table-bordered table-striped">
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th>Practice</th>
                                <th>Provider</th>
                                <th>Status</th>
                                <th>Date</th>
                            </tr>
                            </thead>
                            <tbody>

                            @foreach ($shifts as $shift)
                                <tr>
                                    <td><a href="{{ route('admin.shifts.show', $shift) }}">{{ $shift->id }}</a></td>
                                    <td>{{ $shift->practice->practice_name }}</td>
                                    <td>
                                        {{ $shift->providersName() }}
                                    </td>
                                    <td>
                                        @include('admin.shift._status', $shift)
                                    </td>
                                    <td>{{ $shift->period() }}</td>
                                </tr>
                            @endforeach

                            </tbody>
                        </table>

                        {{ $shifts->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
