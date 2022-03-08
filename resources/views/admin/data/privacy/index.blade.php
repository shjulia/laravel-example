@extends('layouts.main')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card">
                    <div class="card-header">Privacy policy</div>
                    <div class="card-body">
                        <p><a href="{{ route('admin.data.privacy.create') }}" class="btn btn-success">Add new</a></p>

                        <table class="table table-bordered table-striped">
                            <thead>
                            <tr>
                                <th>Date</th>
                                <th>Author</th>
                                <th></th>
                            </tr>
                            </thead>
                            <tbody>

                            @foreach ($privacy as $p)
                                <tr>
                                    <td><a href="{{ route('admin.data.privacy.show', $p) }}">{{ formatedTimestamp($p->created_at) }}</a></td>
                                    <td><a href="{{ route('admin.users.show', $p->admin_id) }}">{{ $p->admin->full_name }}</a></td>
                                    <td><a href="{{ route('admin.data.privacy.create', $p) }}" class="btn btn-success">Add new from this</a></td>
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
