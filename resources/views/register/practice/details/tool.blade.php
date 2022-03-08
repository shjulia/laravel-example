@extends('layouts.main')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <a href="{{ route('practice.details.secondary') }}" @click="$loading.show()" class="back"><i class="fa fa-chevron-left"></i> BACK</a>
                        <h2 class="detailsh2">What Practice Management software have you used in the past?</h2>
                        @include('register.practice.details.partials._form-tool', [
                            'fromAction' => route('practice.details.toolSave'),
                            'showLinks' => true,
                        ])
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
