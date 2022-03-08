@extends('layouts.main')

@section('content')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">Queue Logs</div>
                    <div class="card-body">
                        <iframe src="{{ url('/horizon') }}" frameborder="0" width="100%" style="height: 100vh"></iframe>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
