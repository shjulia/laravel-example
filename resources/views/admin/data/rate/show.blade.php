@extends('layouts.main')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card">
                    <div class="card-header">Rate {{ $rate->title }}</div>
                    <div class="card-body">
                        <div class="d-flex flex-row mb-3">
                            <a href="{{ route('admin.data.rates.edit', $rate) }}" class="btn btn-primary mr-1">Edit</a>

                            <form method="POST" action="{{ route('admin.data.rates.destroy', $rate) }}" class="mr-1">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-danger delete-button-alert">Delete</button>
                            </form>
                            <a href="{{ route('admin.data.rates.index') }}" class="btn btn-success mr-1">List</a>
                        </div>

                        <table class="table table-bordered table-striped">
                            <tbody>
                            <tr>
                                <th>ID</th><td>{{ $rate->id }}</td>
                            </tr>
                            <tr>
                                <th>Title</th><td>{{ $rate->title }}</td>
                            </tr>
                            <tr>
                                <th>Positions</th>
                                <td>
                                    @foreach($rate->positions as $position)
                                        <p><b>Position: </b>{{ $position->title }}</p>
                                        <p><b>Rate: </b>${{ $position->pivot->rate }}</p>
                                        <p><b>Minimum profit: </b>${{ $position->pivot->minimum_profit }}</p>
                                        <p><b>Surge price: </b>${{ $position->pivot->surge_price }}</p>
                                        <p><b>Max day price: </b>${{ $position->pivot->max_day_rate }}</p>
                                        <hr/>
                                    @endforeach
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
