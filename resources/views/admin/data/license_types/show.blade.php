@extends('layouts.main')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card">
                    <div class="card-header">Type {{ $licenseType->title }}</div>
                    <div class="card-body">
                        <div class="d-flex flex-row mb-3">
                            <a href="{{ route('admin.data.license_types.edit', $licenseType) }}" class="btn btn-primary mr-1">Edit</a>

                            <form method="POST" action="{{ route('admin.data.license_types.destroy', $licenseType) }}" class="mr-1">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-danger delete-button-alert">Delete</button>
                            </form>
                            <a href="{{ route('admin.data.license_types.index') }}" class="btn btn-success mr-1">List</a>
                        </div>

                        <table class="table table-bordered table-striped">
                            <tbody>
                            <tr>
                                <th>ID</th><td>{{ $licenseType->id }}</td>
                            </tr>
                            <tr>
                                <th>Title</th><td>{{ $licenseType->title }}</td>
                            </tr>
                            <tr>
                                <th>Positions</th>
                                <td>
                                    @foreach($licenseType->licenseTypePositions as $licenseTypePositions)
                                        <p><b>Position: </b>{{ $licenseTypePositions->position->title }}</p>
                                        <p><b>Is Required: </b>{{ $licenseTypePositions->required ? 'yes' : 'no' }}</p>
                                        <p><b>States: </b>
                                            @foreach($licenseTypePositions->states as $state)
                                            {{ $state->title . ', ' }}
                                            @endforeach
                                        </p>
                                        <hr/>
                                    @endforeach
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
