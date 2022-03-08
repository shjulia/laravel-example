@extends('layouts.main')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card">
                    <div class="card-header">Regions</div>
                    <div class="card-body">
                        <div class="d-flex flex-row mb-3">
                            <a href="{{ route('admin.data.location.region.create') }}" class="btn btn-primary mr-1">Create Region</a>
                            <a href="{{ route('admin.data.location.state.index') }}" class="btn btn-secondary mr-1">All States</a>
                        </div>

                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                </tr>
                            </thead>
                            <tbody>

                            @foreach ($regions as $region)
                                <tr>
                                    <td><a href="{{ route('admin.data.location.region.show', $region) }}">{{ $region->name }}</a></td>
                                </tr>
                            @endforeach

                            </tbody>
                        </table>

                        {{ $regions->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

