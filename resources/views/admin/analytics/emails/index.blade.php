@extends('layouts.main')

@section('content')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">Marketing emails</div>
                    <div class="card-body">
                        <table class="table table-bordered table-striped">
                            <thead>
                            <tr>
                                <th>Subject</th>
                                <th>Sent</th>
                                <th>Opened</th>
                            </tr>
                            </thead>
                            <tbody>

                            @foreach ($emails as $subject => $values)
                                <tr>
                                    <td><a href="{{ route('admin.analytics.emails.show', $values['key']) }}">{{ $subject }}</a></td>
                                    <td>{{ $values['amount'] }}</td>
                                    <td>{{ $values['openedAmount'] }}</td>
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
