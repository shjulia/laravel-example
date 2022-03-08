@extends('layouts.main')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card">
                    <div class="card-header">Score bubbles</div>
                    <div class="card-body">
                        <p><a href="{{ route('admin.data.scores.create') }}" class="btn btn-success">Add score bubble</a></p>

                        <table class="table table-bordered table-striped">
                            <thead>
                            <tr>
                                <th>Title</th>
                                <th>For type</th>
                                <th>Active</th>
                            </tr>
                            </thead>
                            <tbody>

                            @foreach ($scores as $score)
                                <tr>
                                    <td><a href="{{ route('admin.data.scores.show', $score) }}">{{ $score->title }}</a></td>
                                    <td>{{ $score->for_type }}</td>
                                    <td>{{ $score->active ? 'Yes' : 'No' }}</td>
                                </tr>
                            @endforeach

                            </tbody>
                        </table>

                        {{ $scores->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
