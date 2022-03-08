@extends('layouts.main')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card">
                    <div class="card-header">Templates</div>
                    <div class="card-body">
                        <p class="mt-2"></p>
                        <a href="{{ route('admin.newsletter.template.create') }}" class="btn btn-success mb-2 mr-2">Create Template</a>
                        <a href="{{ route('admin.newsletter.newsletter.index') }}" class="btn btn-success mb-2">NewsLetters</a>
                        <table class="table table-bordered table-striped">
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th>Title</th>
                                <th>Created At</th>
                                <th></th>
                            </tr>
                            </thead>
                            <tbody>

                            @foreach ($templates as $template)
                                <tr>
                                    <td><a href="{{ route('admin.newsletter.template.edit', $template) }}">{{ $template->id }}</a></td>
                                    <td>{{ $template->title }}</td>
                                    <td>{{ formatedTimestamp($template->created_at) }}</td>
                                    <td>
                                        <a href="{{ route('admin.newsletter.template.edit', $template) }}" class="btn btn-primary mr-1 mb-1">Edit</a>
                                        <form method="POST" action="{{ route('admin.newsletter.template.destroy', $template) }}" class="mr-1">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-danger delete-button-alert">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
