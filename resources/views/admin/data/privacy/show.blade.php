@extends('layouts.main')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card">
                    <div class="card-header">Privacy policy {{ formatedTimestamp($privacy->created_at) }}</div>
                    <div class="card-body">
                        <a href="{{ route('admin.data.privacy.index') }}" class="btn btn-success mr-1">List</a><br/><br/>
                        {!! $privacy->text !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

