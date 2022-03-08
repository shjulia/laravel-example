@extends('layouts.main')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <a href="{{ route('practice.details.base') }}" @click="$loading.show()" class="back"><i class="fa fa-chevron-left"></i> BACK</a>
                        <h2 class="detailsh2">Practice Details Continued</h2>
                        @include('register.practice.details.partials._form-secondary', [
                            'fromAction' => route('practice.details.secondarySave'),
                            'showLinks' => true,
                        ])
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
