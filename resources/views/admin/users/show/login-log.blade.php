@extends('layouts.main')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card">
                    <div class="card-header">User {{ $user->full_name }} logins log</div>
                    <div class="card-body">
                        <a href="{{ route('admin.users.show', $user) }}" class="btn btn-success mr-1 mb-2" >User data</a>
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Login time</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($logs as $log)
                                    <tr>
                                        <td>
                                            {{ formatedTimestamp($log->created_at) }}
                                        </td>
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
