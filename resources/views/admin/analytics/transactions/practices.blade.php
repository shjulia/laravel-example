@extends('admin.analytics.transactions.transactions-layout')

@section('cont')
    @include('admin.analytics.transactions._export-form', [
        'action' => route('admin.analytics.transactions.practicesExport')
    ])
    <table class="table table-bordered table-striped">
        <thead>
        <tr>
            <th>Shift</th>
            <th>Practice</th>
            <th>Stripe Charge ID</th>
            <th>Amount</th>
            <th>Status</th>
            <th>Refund Amount</th>
            <th>Is main</th>
            <th>Created at</th>
        </tr>
        </thead>
        <tbody>

        @foreach ($charges as $charge)
            <tr>
                <td>
                    <a href="{{ route('admin.shifts.show', $charge->shift) }}">{{ $charge->shift->id }}</a>
                </td>
                <td>
                    <a href="{{ route('admin.users.show', $charge->shift->creator) }}">{{ $charge->practice ? $charge->practice->practice_name : '' }}</a>
                </td>
                <td>
                    <a target="_blank" href="{{ walletClientUrl($charge->practice->practiceCreator()) }}">{{ $charge->charge_stripe_id }}</a>
                </td>
                <td>${{ $charge->amount }}</td>
                <td>{{ $charge->paymentStatusString() }}</td>
                <td>{{ $charge->isRefund() ? 'fully refund' : ($charge->refund_amount ? ('partial refund $' . $charge->refund_amount) : '---') }}</td>
                <td>{{ $charge->is_main ? 'main' : 'part' }}</td>
                <td>{{ formatedTimestamp(\Illuminate\Support\Carbon::createFromTimeString($charge->created)) }}</td>
            </tr>
        @endforeach

        </tbody>
    </table>
    {{ $charges->links() }}
@endsection

