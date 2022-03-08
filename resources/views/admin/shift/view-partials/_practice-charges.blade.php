@if (!$shift->charges->isEmpty())
    <div class="card mb-2">
        <div class="card-header">Shift payment history (practice)</div>
        <div class="card-body">
            <table class="table table-bordered table-striped">
                <thead>
                <tr>
                    <th>Core Charge ID</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th>Refund Amount</th>
                    <th>Is main</th>
                    <th>Created at</th>
                </tr>
                </thead>
                <tbody>

                @foreach ($shift->charges as $charge)
                    <tr>
                        <td><a target="_blank" href="{{ walletClientUrl($shift->practice->practiceCreator()) }}">{{ $charge->charge_stripe_id }}</a></td>
                        <td>${{ $charge->amount }}</td>
                        <td>{{ $shift->paymentStatusString($charge) }}</td>
                        <td>{{ $charge->isRefund() ? ($charge->refund_amount ? ('partial refund $' . $charge->refund_amount) : 'fully refund') : '--' }}</td>
                        <td>{{ $charge->is_main ? 'main' : 'part' }}</td>
                        <td>{{ formatedTimestamp(\Illuminate\Support\Carbon::createFromTimeString($charge->created)) }}</td>
                    </tr>
                @endforeach

                </tbody>
            </table>
        </div>
    </div>
@endif
