@extends('layouts.main')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card">
                    <div class="card-header">Position {{ $position->title }}</div>
                    <div class="card-body">
                        <div class="d-flex flex-row mb-3">
                            <a href="{{ route('admin.data.positions.edit', $position) }}" class="btn btn-primary mr-1">Edit</a>

                            <form method="POST" action="{{ route('admin.data.positions.destroy', $position) }}" class="mr-1">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-danger delete-button-alert">Delete</button>
                            </form>
                            <a href="{{ route('admin.data.positions.index') }}" class="btn btn-success mr-1">List</a>
                        </div>

                        <table class="table table-bordered table-striped">
                            <tbody>
                                <tr>
                                    <th>ID</th><td>{{ $position->id }}</td>
                                </tr>
                                <tr>
                                    <th>Title</th><td>{{ $position->title }}</td>
                                </tr>
                                <tr>
                                    <th>Industry</th><td>{{ $position->industry->title }}</td>
                                </tr>
                                <tr>
                                    <th>Parent</th><td>{{ $position->parent ? $position->parent->title : '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Fee</th><td>{{ $position->fee }}</td>
                                </tr>
                                <tr>
                                    <th>Minimum profit</th><td>{{ $position->minimum_profit }}</td>
                                </tr>
                                <tr>
                                    <th>Surge price</th><td>{{ $position->surge_price }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
