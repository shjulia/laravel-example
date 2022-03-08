@extends('admin.analytics.transactions.transactions-layout')

@section('cont')
    <table class="table table-bordered table-striped">
        <thead>
        <tr>
            <th>Invitor</th>
            <th>Referred User</th>
            <th>PS Charge ID</th>
            <th>Payment system</th>
            <th>Amount</th>
            <th>Status</th>
            <th>Created at</th>
            <th></th>
        </tr>
        </thead>
        <tbody>

        @foreach ($invites as $invite)
            <tr>
                <td>
                    <a href="{{ route('admin.users.show', $invite->referral->user) }}">{{ $invite->referral->user->full_name }}</a>
                </td>
                <td>
                    <a href="{{ route('admin.users.show', $invite->user) }}">{{ $invite->user->full_name }}</a>
                </td>
                <td><a href="{{ $invite->charge_id }}">{{ $invite->charge_id }}</a></td>
                <td>{{ $invite->payment_system }}</td>
                <td>${{ $invite->bonus_value }}</td>
                <td>{{ $invite->payment_status ?: $invite->status }}</td>
                <td>{{ formatedTimestamp($invite->created_at) }}</td>
                <td>
                    @if (!$invite->isPaidStatus())
                        <a class="btn btn-primary" href="{{ route('admin.users.edit.invite', $invite)  }}">Edit</a>
                    @endif
                </td>
            </tr>
        @endforeach

        </tbody>
    </table>
    {{ $invites->links() }}
@endsection
