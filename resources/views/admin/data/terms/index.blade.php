@extends('layouts.main')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card">
                    <div class="card-header">Terms and conditions</div>
                    <div class="card-body">
                        <p><a href="{{ route('admin.data.terms.create') }}" class="btn btn-success">Add new</a></p>

                        <table class="table table-bordered table-striped">
                            <thead>
                            <tr>
                                <th>Date</th>
                                <th>Author</th>
                                <th></th>
                            </tr>
                            </thead>
                            <tbody>

                            @foreach ($terms as $term)
                                <tr>
                                    <td><a href="{{ route('admin.data.terms.show', $term) }}">{{ formatedTimestamp($term->created_at) }}</a></td>
                                    <td><a href="{{ route('admin.users.show', $term->admin_id) }}">{{ $term->admin->full_name }}</a></td>
                                    <td><a href="{{ route('admin.data.terms.create', $term) }}" class="btn btn-success">Add new from this</a></td>
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
