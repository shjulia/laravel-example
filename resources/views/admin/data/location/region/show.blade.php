@extends('layouts.main')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card">
                    <div class="card-header">{{ $region->name }}</div>
                    <div class="card-body">
                        <div class="d-flex flex-row mb-3">
                            <a href="{{ route('admin.data.location.region.index') }}" class="btn btn-success mr-1">List</a>
                            <a href="{{ route('admin.data.location.region.edit', $region) }}" class="btn btn-secondary mr-1">Edit</a>
                            @include('admin.data.location.region.includes.delete')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('admin.data.location.state.includes.list')
@endsection