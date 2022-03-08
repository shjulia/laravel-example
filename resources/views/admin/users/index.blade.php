@extends('layouts.main')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">Users</div>
                    <div class="card-body">
                        @can('manage-users')
                            <p>
                                {{-- <a href="{{ route('admin.users.create') }}" class="btn btn-success disabled">Add User</a> --}}
                                <a href="{{ $test ? route('admin.users.index') : route('admin.users.indext', ['test' => 1]) }}" class="btn btn-primary btn-sm">{{ $test ? 'Show Real users list' : 'Show Test users list' }}</a>
                                <a href="{{ $withRejected ? route('admin.users.index') : route('admin.users.indexr', ['rejected' => 1]) }}" class="btn btn-primary btn-sm">{{ $withRejected ? 'Show not rejected users list' : 'Show Rejected users list' }}</a>
                                <a href="{{ $deactivated ? route('admin.users.index') : route('admin.users.index-deactivated', ['deactivated' => 1]) }}" class="btn btn-primary btn-sm">{{ $deactivated ? 'Show active users' : 'Show deactivated users' }}</a>
                                <a href="{{ route('admin.users.approvalList') }}" class="btn btn-success btn-sm">Approval Lists</a>
                                @if (!$test && !$withRejected && !$deactivated)
                                    <a href="{{ route('admin.users.exportUsers') }}" class="btn btn-success btn-sm">Export users</a>
                                @endif
                            </p>
                        @endcan
                        <form action="?" method="GET">
                            <div class="row">
                                <div class="col-sm-1">
                                    <div class="form-group">
                                        <label for="id" class="col-form-label">ID</label>
                                        <input id="id" class="form-control" name="id" value="{{ request('id') }}">
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <label for="first_name" class="col-form-label">First name</label>
                                        <input id="first_name" class="form-control" name="first_name" value="{{ request('first_name') }}">
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <label for="last_name" class="col-form-label">Last name</label>
                                        <input id="last_name" class="form-control" name="last_name" value="{{ request('last_name') }}">
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <label for="email" class="col-form-label">Email</label>
                                        <input id="email" class="form-control" name="email" value="{{ request('email') }}">
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <label for="status" class="col-form-label">Status</label>
                                        <select id="status" class="form-control" name="status">
                                            <option value=""></option>
                                            @foreach ($statuses as $value => $label)
                                                <option value="{{ $value }}"{{ $value == request('status') ? ' selected' : '' }}>{{ $label }}</option>
                                            @endforeach;
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <label for="role" class="col-form-label">Role</label>
                                        <select id="role" class="form-control" name="role">
                                            <option value=""></option>
                                            @foreach ($roles as $role)
                                                <option value="{{ $role->id }}"{{ $role->id == request('role') ? ' selected' : '' }}>{{ $role->title }}</option>
                                            @endforeach;
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <label for="position" class="col-form-label">Position</label>
                                        <select id="position" class="form-control" name="position">
                                            <option value=""></option>
                                            @foreach ($positions as $position)
                                                <option value="{{ $position->id }}"{{ $position->id == request('position') ? ' selected' : '' }}>{{ $position->title }}</option>
                                            @endforeach;
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <label for="states" class="col-form-label">States</label>
                                        <select id="states" class="form-control" name="state">
                                            <option value=""></option>
                                            @foreach ($states as $state)
                                                <option value="{{ $state->short_title }}"{{ $state->short_title == request('state') ? ' selected' : '' }}>{{ $state->title }}</option>
                                            @endforeach;
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-1">
                                    <div class="form-group">
                                        <label class="col-form-label">&nbsp;</label><br />
                                        <button type="submit" class="btn btn-primary">Search</button>
                                    </div>
                                </div>
                            </div>
                        </form>

                        <table class="table table-bordered table-striped">
                            <thead>
                            <tr>
                                <th>Full name</th>
                                <th>E-mail</th>
                                <th>Status</th>
                                <th>Role</th>
                                    @can('manage-users')
                                <th style="width: 81px;"></th>
                                @endcan
                            </tr>
                            </thead>
                            <tbody>

                            @foreach ($users as $user)
                                <tr>
                                    <td><a href="{{ route('admin.users.show', $user) }}">{{ $user->first_name . ' ' . $user->last_name }}</a></td>
                                    <td>{{ $user->email }}</td>
                                    <td>
                                        @if($user->is_rejected)
                                            <span class="badge badge-danger">Rejected</span>
                                        @else
                                            @if ($user->isProvider())
                                                @if ($user->specialist->isWaiting())
                                                    <span class="badge badge-secondary">Provider: Waiting</span>
                                                @elseif ($user->specialist->isApproved())
                                                    <span class="badge badge-primary">Provider: Active</span>
                                                @elseif ($user->specialist->isDuplicate())
                                                    <span class="badge badge-danger">Provider: Duplicate</span>
                                                @endif
                                            @endif
                                            @if ($user->isPractice())
                                                @if ($user->practice->isWaiting())
                                                    <span class="badge badge-secondary">Practice: Waiting</span>
                                                @elseif ($user->practice->isApproved())
                                                    <span class="badge badge-primary">Practice: Active</span>
                                                @endif
                                            @endif
                                        @endif
                                    </td>
                                    <td>
                                        @include('admin.users._show-role')
                                    </td>
                                    @can('manage-users')
                                    <td>
                                        <form action="{{ route('admin.users.setToTest', $user) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-secondary action-button-alert">{{ $user->isTestAccount() ? 'Remove from test' : 'To test' }}</button>
                                        </form>
                                        @can('login-as')
                                            <form method="POST" action="{{ route('admin.users.login-as', $user) }}">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-primary mt-1">LoginAs</button>
                                            </form>
                                        @endcan
                                    </td>
                                    @endcan
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
