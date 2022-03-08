@extends('layouts.main')

@section('content')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card">
                    <div class="card-header">Edit area {{ $area->name }} in <b>{{ $state->title }}</b></div>
                    <div class="card-body">
                        <div class="d-flex flex-row mb-3">
                            <a href="{{ route('admin.data.location.area.index', [$state]) }}" class="btn btn-success mr-1">Back</a>
                            @include('admin.data.location.area.includes.delete')
                        </div>

                        @include('admin.data.location.area.includes.form')
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection