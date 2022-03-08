@extends('layouts.main')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card">
                    <div class="card-header">Sign up auto-saves</div>
                    <div class="card-body">
                        <p>
                            <a href="{{ route('admin.users.exportAutosaves') }}" class="btn btn-success btn-sm">Export</a>
                        </p>
                        <form action="?" method="GET">
                            <div class="row">
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="first_name" class="col-form-label">First name</label>
                                        <input id="first_name" class="form-control" name="first_name" value="{{ request('first_name') }}">
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="last_name" class="col-form-label">Last name</label>
                                        <input id="last_name" class="form-control" name="last_name" value="{{ request('last_name') }}">
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="email" class="col-form-label">Email</label>
                                        <input id="email" class="form-control" name="email" value="{{ request('email') }}">
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label class="col-form-label">&nbsp;</label><br />
                                        <button type="submit" class="btn btn-primary">Search</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Full name</th>
                                    <th>E-mail</th>
                                    <th>Created At</th>
                                    <th>Updated At</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                            @foreach ($users as $user)
                                <tr>
                                    <td>{{ $user->first_name . ' ' . $user->last_name }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>{{ formatedTimestamp($user->created_at) }}</td>
                                    <td>{{ formatedTimestamp($user->updated_at) }}</td>
                                    <td>
                                        <form action="{{ route('admin.users.autosaves.delete', $user) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-danger delete-button-alert"><i class="fa fa-trash"></i> remove</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>

                        {{ $users->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
