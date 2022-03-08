@extends('layouts.main')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-xl-7 col-12">
            <div class="card details">
                <div class="card-body">
                    <a href="{{ url()->previous() }}" class="back"><i class="fa fa-chevron-left"></i> BACK</a>
                    <h2 class="detailsh2">Licensure</h2>
                    <div class="text-center">
                        <span class="text-center">Please enter your medical licenses information</span>
                    </div>
                    @include('license._form',  [
                        'uploadPhotoUrl' => route('upload.Medical'),
                        'action' => route('license.create'),
                        'removeLicenseUrl' => route('license.remove'),
                        'saveOneAction' => route('one-license.save')
                    ])
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
