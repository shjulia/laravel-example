@extends('layouts.main')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card">
                    <div class="card-header">License Types</div>
                    <div class="card-body">
                        <p><a href="{{ route('admin.data.license_types.create') }}" class="btn btn-success">Add type</a></p>

                        <table class="table table-bordered table-striped">
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th>Title</th>
                                <th>Position</th>
                            </tr>
                            </thead>
                            <tbody>

                            @foreach ($licenseTypes as $type)
                                <tr>
                                    <td>{{ $type->id }}</td>
                                    <td><a href="{{ route('admin.data.license_types.show', $type) }}">{{ $type->title }}</a></td>
                                    <td>
                                        @foreach($type->positions as $position)
                                            {{ $position->title . ', ' }}
                                        @endforeach
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
