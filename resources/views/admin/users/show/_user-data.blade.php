<h3>User data</h3>

@if(!$user->isSignupFinished())
    <div class="alert alert-danger">User hasn't already finished sign-up process</div>
@endif
<div class="d-flex flex-row mb-3">
    @can('manage-users')
        <a href="{{ route('admin.users.edit.userData', $user) }}" class="btn btn-primary mr-1 {{ $user->isProvider() || $user->isPractice() ? '' : 'disabled' }}" >Edit</a>

        <form method="POST" action="{{ route('admin.users.destroy', $user) }}" class="mr-1 delete-user-form">
            @csrf
            @method('DELETE')
            <button class="btn btn-danger delete-button-alert">Delete</button>
        </form>

        @if (!$user->isRejected())
            <form method="POST" action="{{ route('admin.users.reject', $user) }}" class="mr-1 delete-user-form">
                @csrf
                <button class="btn btn-danger delete-button-alert">Reject</button>
            </form>
        @else
            <form method="POST" action="{{ route('admin.users.un-reject', $user) }}" class="mr-1 delete-user-form">
                @csrf
                <button class="btn btn-warning delete-button-alert">Un-reject</button>
            </form>
        @endif

        @if ($user->isAccountActive())
            <form method="POST" action="{{ route('deactivate-account', $user) }}" class="mr-1 delete-user-form">
                @csrf
                <button class="btn btn-warning">Deactivate</button>
            </form>
        @else
            <form method="POST" action="{{ route('activate-account', $user) }}" class="mr-1 delete-user-form">
                @csrf
                <button class="btn btn-warning">Activate</button>
            </form>
        @endif

        <a href="{{ route('admin.users.showEmails', $user) }}" class="btn btn-secondary mr-1">Emails log</a>
        <a href="{{ route('admin.users.showApproves', $user) }}" class="btn btn-secondary mr-1">Approvals log</a>
        <a href="{{ route('admin.users.show-logins', $user) }}" class="btn btn-secondary mr-1">Logins log</a>
    @endcan
    <a href="{{ route('admin.users.index') }}" class="btn btn-success mr-1">List</a>
</div>

<table class="table table-bordered table-striped">
    <tbody>
        <tr>
            <th>ID</th><td>{{ $user->id }}</td>
        </tr>
        <tr>
            <th>Full name</th><td>{{ $user->first_name . ' ' . $user->last_name }}</td>
        </tr>
        <tr>
            <th>Email</th><td>{{ $user->email }}</td>
        </tr>
        @if ($user->isProvider() || $user->isPractice())
            <tr>
                <th>Status</th>
                <td>
                    @if ($user->isProvider())
                        @if ($user->specialist->isWaiting())
                            <span class="badge badge-secondary">Provider: Waiting</span>
                        @elseif ($user->specialist->isApproved())
                            <span class="badge badge-primary">Provider: Active</span>
                            @if ($user->specialist->approval_reason)
                                <p class="mb-0">reason: <b>{{ $user->specialist->approval_reason }}</b></p>
                            @endif
                        @endif
                    @endif
                    @if ($user->isPractice())
                        @if ($user->practice->isWaiting())
                            <span class="badge badge-secondary">Practice: Waiting</span>
                        @elseif ($user->practice->isApproved())
                            <span class="badge badge-primary">Practice: Active</span>
                        @endif
                    @endif
                </td>
            </tr>
        @endif
        @if ($user->isRejected())
            <tr>
                <th>Rejected</th>
                <td>
                    <span class="badge badge-danger">Rejected</span>
                </td>
            </tr>
        @endif
        <tr>
            <th>Role</th>
            <td>
                @include('admin.users._show-role')
            </td>
        </tr>
        @if ($user->isPractice())
            <tr>
                <th>Role in practice</th>
                <td>
                    <span class="badge badge-primary">{{ $user->practice->pivot->practice_role }}</span>
                </td>
            </tr>
        @endif
        <tr>
            <th>Phone</th><td>{{ $user->phone }}</td>
        </tr>
        <tr>
            <th>Inviter</th>
            @if($user->invite)
                <td><a href="{{ route('admin.users.show', $user->invite->referral_id) }}">{{ $user->invite->referral->user->full_name }}</a></td>
            @else
                <td><a class="btn btn-info" href="{{ route('admin.users.setInviter', $user) }}">set inviter</a></td>
            @endif
        </tr>
        <tr>
            <th>Subscriptions</th>
            <td>
                <p class="mb-0">Web push notifications: <b>{{ $user->pushSubscriptions->isEmpty() ? 'NO' : 'YES' }}</b></p>
                <p class="mb-0">IOS push notifications: <b>{{ $user->players->isEmpty() ? 'NO' : 'YES' }}</b></p>
            </td>
        </tr>
        <tr>
            <th>Created At</th><td>{{ formatedTimestamp($user->created_at) }}</td>
        </tr>
        <tr>
            <th>Password set at</th>
            <td>
                {{ $user->passwordSetup ? formatedTimestamp($user->passwordSetup->created_date) : 'not set' }}
                <form method="post" action="{{ route('admin.users.reset-password-email', $user) }}">
                    @csrf
                    <button type="submit" class="btn btn-warning btn-sm">Send "Reset password" Email</button>
                </form>
            </td>
        </tr>
    </tbody>
</table>
