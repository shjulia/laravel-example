@extends('layouts.main')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card">
                    <div class="card-header">Practice Management Software</div>
                    <div class="card-body">
                        <p><a href="{{ route('admin.data.tools.create') }}" class="btn btn-success">Add Tool</a></p>

                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Title</th>
                                    <th></th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>

                            @foreach ($tools as $tool)
                                <tr>
                                    <td>{{ $tool->id }}</td>
                                    <td>{{ $tool->title }}</td>
                                    <td><a class="btn btn-primary" href="{{ route('admin.data.tools.edit', $tool) }}">Edit</a></td>
                                    <td>
                                        <form action="{{ route('admin.data.tools.destroy', $tool) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger delete-button-alert">Remove</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach

                            </tbody>
                        </table>

                        {{ $tools->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
