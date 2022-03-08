@extends('layouts.main')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">Edit NewsLatter</div>
                    <div class="card-body">
                        <p class="mt-2"></p>
                        <newsletter-form
                            save-url="{{ route('admin.newsletter.newsletter.update', $newsletter) }}"
                            index-url="{{ route('admin.newsletter.newsletter.index') }}"
                            :templates="{{ $templates }}"
                            :roles="{{ $roles }}"
                            id="{{ $newsletter->id }}"
                            :news-letter="{{ $newsletter }}"
                        ></newsletter-form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
