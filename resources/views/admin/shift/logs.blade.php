@extends('layouts.main')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">Shift {{ $shift->id }} log</div>
                    <div class="card-body">
                        <a href="{{ route('admin.shifts.show', $shift) }}" class="btn btn-success mb-2 mr-1">Shift view</a>

                        <table class="table table-bordered table-striped">
                            <thead>
                            <tr>
                                <th>User</th>
                                <th>Action</th>
                                <th>Created At</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($logs as $log)
                                <tr>
                                    <td>
                                        @if ($log->user_id)
                                            <a href="{{ route('admin.users.show', $log->user_id) }}">{{ $log->user->full_name}}</a>
                                        @else
                                            -system-
                                        @endif
                                    </td>
                                    <td>{{ $log->action }}</td>
                                    <td>{{ formatedTimestamp($log->created_at) }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
