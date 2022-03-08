@extends('layouts.main')

@section('content')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">Logs</div>
                    <div class="card-body">
                        <table class="table table-bordered table-striped">
                            <thead>
                            <tr>
                                <th>Log file name</th>
                                <th>Size</th>
                                <th>Last modified</th>
                                <th></th>
                            </tr>
                            </thead>
                            <tbody>

                            @foreach ($files as $file)
                                <tr>
                                    <td><a href="{{ route('admin.analytics.logs.view', $file['file_name']) }}">{{ $file['file_name'] }}</a></td>
                                    <td>{{ $file['file_size'].' MB' }}</td>
                                    <td>{{ $file['last_modified'] }}</td>
                                    <td>
                                        <form method="POST" action="{{ route('admin.analytics.logs.delete', $file['file_name']) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger">remove</button>
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
