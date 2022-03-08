@extends('layouts.main')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">Create Template</div>
                    <div class="card-body">
                        <p class="mt-2"></p>
                        <email-create
                            save-url="{{ route('admin.newsletter.template.store') }}"
                            index-url="{{ route('admin.newsletter.template.index') }}"
                        ></email-create>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
