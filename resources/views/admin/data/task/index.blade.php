@extends('layouts.main')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card">
                    <div class="card-header">Routine tasks</div>
                    <div class="card-body">
                        <p><a href="{{ route('admin.data.tasks.create') }}" class="btn btn-success">Add task</a></p>

                        <table class="table table-bordered table-striped">
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th>Title</th>
                                <th>Position</th>
                            </tr>
                            </thead>
                            <tbody>

                            @foreach ($tasks as $task)
                                <tr>
                                    <td>{{ $task->id }}</td>
                                    <td><a href="{{ route('admin.data.tasks.show', $task) }}">{{ $task->title }}</a></td>
                                    <td>{{ $task->position->title }}</td>
                                </tr>
                            @endforeach

                            </tbody>
                        </table>

                        {{ $tasks->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
