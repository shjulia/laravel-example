@extends('layouts.main')

@section('content')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">File {{ $fileName }}</div>
                    <div class="card-body">
                        <pre>
                            {{ $fileContent }}
                        </pre>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection