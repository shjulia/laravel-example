@extends('layouts.main')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card">
                    <div class="card-header">Rates</div>
                    <div class="card-body">
                        <p><a href="{{ route('admin.data.rates.create') }}" class="btn btn-success">Add Rate</a></p>

                        <table class="table table-bordered table-striped">
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th>Title</th>
                            </tr>
                            </thead>
                            <tbody>

                            @foreach ($rates as $rate)
                                <tr>
                                    <td>{{ $rate->id }}</td>
                                    <td><a href="{{ route('admin.data.rates.show', $rate) }}">{{ $rate->title }}</a></td>
                                </tr>
                            @endforeach

                            </tbody>
                        </table>

                        {{ $rates->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
