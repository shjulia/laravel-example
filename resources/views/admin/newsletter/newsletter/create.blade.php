@extends('layouts.main')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">Create NewsLatter</div>
                    <div class="card-body">
                        <p class="mt-2"></p>
                        <newsletter-form
                            save-url="{{ route('admin.newsletter.newsletter.store') }}"
                            index-url="{{ route('admin.newsletter.newsletter.index') }}"
                            :templates="{{ $templates }}"
                            :roles="{{ $roles }}"
                        ></newsletter-form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
