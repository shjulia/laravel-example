@extends('layouts.main')

@section('content')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card">
                    <div class="card-header">Create new region</div>
                    <div class="card-body">
                        <div class="d-flex flex-row mb-3">
                            <a href="{{ route('admin.data.location.region.index') }}" class="btn btn-success mr-1">Back</a>
                        </div>

                        @include('admin.data.location.region.includes.form')
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection