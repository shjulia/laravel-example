@extends('layouts.main')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">Edit Template {{ $template->title }}</div>
                    <div class="card-body">
                        <p class="mt-2"></p>
                        <email-create
                            save-url="{{ route('admin.newsletter.template.update', ['template' => $template->id]) }}"
                            :content="{{ $template->json_content }}"
                            title="{{ $template->title }}"
                            index-url="{{ route('admin.newsletter.template.index') }}"
                            id="{{ $template->id }}"
                        ></email-create>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
