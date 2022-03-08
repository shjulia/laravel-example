@extends('layouts.main')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card">
                    <div class="card-header">{{ $city->name }}</div>
                    <div class="card-body">
                        <div class="d-flex flex-row mb-3">
                            <a href="{{ route('admin.data.location.state.show', [$state]) }}" class="btn btn-success mr-1">List</a>
                            <a href="{{ route('admin.data.location.city.edit', [$state, $city]) }}" class="btn btn-primary mr-1">Edit</a>
                        </div>

                        <table class="table table-bordered table-striped">
                            <tbody>
                            <tr>
                                <th>Name</th><td>{{ $city->name }}</td>
                            </tr>
                            <tr>
                                <th>Tier</th><td>{{ $city->tier }}</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection