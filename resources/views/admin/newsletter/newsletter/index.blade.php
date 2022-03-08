@extends('layouts.main')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card">
                    <div class="card-header">NewsLetters</div>
                    <div class="card-body">
                        <p class="mt-2"></p>
                        <a href="{{ route('admin.newsletter.newsletter.create') }}" class="btn btn-success mb-2">Create NewsLetter</a>
                        <table class="table table-bordered table-striped">
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th>Template</th>
                                <th>Subject</th>
                                <th>Start Date</th>
                                <th>Status</th>
                                <th></th>
                            </tr>
                            </thead>
                            <tbody>

                            @foreach ($newsLetters as $newsLetter)
                                <tr>
                                    <td><a href="{{ route('admin.newsletter.newsletter.edit', $newsLetter) }}">{{ $newsLetter->id }}</a></td>
                                    <td>{{ $newsLetter->template->title }}</td>
                                    <td>{{ $newsLetter->subject }}</td>
                                    <td>{{ $newsLetter->start_date }}</td>
                                    <td>
                                        @if ($newsLetter->is_finished)
                                            <span class="badge badge-success">Finished</span>
                                        @else
                                            <span class="badge badge-secondary">Not finished</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.newsletter.newsletter.edit', $newsLetter) }}" class="btn btn-primary mr-1 mb-1">Edit</a>
                                        <form method="POST" action="{{ route('admin.newsletter.newsletter.destroy', $newsLetter) }}" class="mr-1">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-danger delete-button-alert">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                        {{ $newsLetters->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
