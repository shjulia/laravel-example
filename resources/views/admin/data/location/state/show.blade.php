@extends('layouts.main')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card">
                    <div class="card-header">{{ $state->title }}</div>
                    <div class="card-body">
                        <div class="d-flex flex-row mb-3">
                            <a href="{{ route('admin.data.location.state.index') }}" class="btn btn-success mr-1">List</a>
                            <a href="{{ route('admin.data.location.area.index', $state) }}" class="btn btn-secondary mr-1">Show Areas</a>
                            <a href="{{ route('admin.data.location.area.create', $state) }}" class="btn btn-primary mr-1">Create Area</a>
                        </div>

                        <table class="table table-bordered table-striped">
                            <tbody>
                            <tr>
                                <th>Title</th><td>{{ $state->title }}</td>
                            </tr>
                            <tr>
                                <th>Short Title</th><td>{{ $state->short_title }}</td>
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