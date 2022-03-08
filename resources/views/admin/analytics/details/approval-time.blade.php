@extends('layouts.main')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card">
                    <div class="card-header">Time to Approval</div>
                    <div class="card-body">
                        <a href="{{ route('admin.analytics.index') }}" class="btn btn-success mr-1" >Back</a>
                        <table class="table table-bordered table-striped">
                            <thead>
                            <tr>
                                <th>User</th>
                                <th>Completed signup at</th>
                                <th>Approved at</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($users as $user)
                                @if (!$user->initialSignupCompletedTime() || !$user->approvedTime())
                                    @continue
                                @endif
                                <tr>
                                    <td><a href="{{ route('admin.users.show', $user) }}">{{ $user->full_name }}</a></td>
                                    <td>{{ formatedTimestamp($user->initialSignupCompletedTime()) }}</td>
                                    <td>{{ formatedTimestamp($user->approvedTime()) }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                        {{ $users->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
