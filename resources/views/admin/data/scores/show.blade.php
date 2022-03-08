@extends('layouts.main')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card">
                    <div class="card-header">Score bubble {{ $score->title }}</div>
                    <div class="card-body">
                        <div class="d-flex flex-row mb-3">
                            <a href="{{ route('admin.data.scores.edit', $score) }}" class="btn btn-primary mr-1">Edit</a>

                            <form method="POST" action="{{ route('admin.data.scores.destroy', $score) }}" class="mr-1">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-danger delete-button-alert">Delete</button>
                            </form>
                            <a href="{{ route('admin.data.scores.index') }}" class="btn btn-success mr-1">List</a>
                        </div>

                        <table class="table table-bordered table-striped">
                            <tbody>
                                <tr>
                                    <th>ID</th><td>{{ $score->id }}</td>
                                </tr>
                                <tr>
                                    <th>Title</th><td>{{ $score->title }}</td>
                                </tr>
                                <tr>
                                    <th>Type</th><td>{{ $score->for_type }}</td>
                                </tr>
                                <tr>
                                    <th>Is active</th><td>{{ $score->active ? 'Yes' : 'No' }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
