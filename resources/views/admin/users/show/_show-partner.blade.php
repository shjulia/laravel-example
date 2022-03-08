<div>
    <h3>Referral info</h3>
    <table class="table table-bordered table-striped">
        <tbody>
        <tr>
            <th>Referral code</th><td>{{ $user->referral->referral_code }}</td>
        </tr>
        <tr>
            <th>Referred users</th><td>{{ $user->referral->referred_amount }}</td>
        </tr>
        <tr>
            <th>Referred money</th><td>${{ $user->referral->referral_money_earned }}</td>
        </tr>
        </tbody>
    </table>

    <h3>Referral invites</h3>
    <table class="table table-bordered table-striped">
        <thead>
        <tr>
            <th>E-mail</th>
            <th>User</th>
            <th>Accept</th>
            <th>Created At</th>
            <th>Bonus value</th>
            <th>Payment status</th>
            <th>Charge ID</th>
            <th>Payment system</th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        @foreach ($user->referral->invites as $invite)
            <tr>
                <td>{{ $invite->email }}</td>
                <td>@if ($invite->user_id) <a href="{{ route('admin.users.show', $invite->user_id) }}">user</a> @else - @endif</td>
                <td>
                    @if (!$invite->accepted)
                        <span class="badge badge-secondary">No respond</span>
                    @else
                        <span class="badge badge-primary">Accepted</span>
                    @endif
                </td>
                <td>{{ formatedTimestamp($invite->created_at) }}</td>
                <td>{{ $invite->bonus_value ? ('$' . $invite->bonus_value) : '---'  }}</td>
                <td>{{ $invite->status ?: 'not worked'  }}</td>
                <td>{{ $invite->charge_id ?: '---'  }}</td>
                <td>{{ $invite->payment_system ?: '---'  }}</td>
                <td>
                    @if (!$invite->isPaidStatus())
                        <a class="btn btn-primary" href="{{ route('admin.users.edit.invite', $invite)  }}">Edit</a>
                    @endif
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
