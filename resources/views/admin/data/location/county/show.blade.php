@extends('layouts.main')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card">
                    <div class="card-header">{{ $county->name }}</div>
                    <div class="card-body">
                        <div class="d-flex flex-row mb-3">
                            <a href="{{ route('admin.data.location.state.show', $state) }}" class="btn btn-success mr-1">List</a>
                            <a href="{{ route('admin.data.location.county.edit', [$state, $county]) }}" class="btn btn-primary mr-1">Edit</a>
                        </div>

                        <table class="table table-bordered table-striped">
                            <tbody>
                            <tr>
                                <th>Name</th><td>{{ $county->name }}</td>
                            </tr>
                            <tr>
                                <th>Tier</th><td>{{ $county->tier }}</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('admin.data.location.city.includes.list')
@endsection