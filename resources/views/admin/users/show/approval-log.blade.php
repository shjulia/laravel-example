@extends('layouts.main')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card">
                    <div class="card-header">User {{ $user->full_name }} approval log</div>
                    <div class="card-body">
                        <a href="{{ route('admin.users.show', $user) }}" class="btn btn-success mr-1" >User data</a>
                        <table class="table table-bordered table-striped">
                            <thead>
                            <tr>
                                <th>Approval Status</th>
                                <th>Admin</th>
                                <th>Action</th>
                                <th>Set at</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($user->approveLogs->reverse() as $log)
                                <tr>
                                    <td>
                                        <span class="badge @if ($log->isApproved()) badge-success @elseif ($log->isChangedByAdmin()) badge-warning @else badge-danger @endif">{{ $log->status }}</span>
                                    </td>
                                    <td>{{ $log->admin ? $log->admin->full_name : '' }}</td>
                                    <td>{{ $log->desc }}</td>
                                    <td>{{ formatedTimestamp($log->created_at) }}</td>
                                </tr>
                            @endforeach
                            <tr>
                                <td><span class="badge badge-primary">auto set waiting</span></td>
                                <td>on register</td>
                                <td></td>
                                <td>{{ formatedTimestamp($user->created_at) }}</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
