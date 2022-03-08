@extends('layouts.main')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">Users</div>
                    <div class="card-body">
                        <p>
                            <a href="{{ route('admin.users.index') }}" class="btn btn-success btn-sm">All Users List</a>
                        </p>
                        <div class="row">
                            <div class="col-sm-4">
                                @include("admin.users._approval-column", ["bg" => "bg-danger", "list" => $lists["red"], "columnName" => "RED"])
                            </div>
                            <div class="col-sm-4">
                                @include("admin.users._approval-column", ["bg" => "bg-warning", "list" => $lists["yellow"], "columnName" => "YELLOW"])
                            </div>
                            <div class="col-sm-4">
                                @include("admin.users._approval-column", ["bg" => "bg-success",  "list" => $lists["green"], "columnName" => "GREEN"])
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
