@extends('layouts.main')

@section('content')
    <div class="container"  id="pushInPage">
        <div class="row justify-content-center">
            <div class="col-xl-6 col-12">
                <div class="card details">
                    <div class="card-body">
                        <a href="{{ route('home') }}" class="back"><i class="fa fa-chevron-left"></i> BACK</a>
                        <h2 class="detailsh2">Update Your Identity</h2>
                        @include("register.provider._identity-forms", [
                            'action' => route('provider.edit.uploadDriver'),
                            'nextAction' => route('home'),
                            'phoneAction' => route('provider.edit.phoneSave'),
                            'formAction' => route('provider.edit.identitySave'),
                            'removeRoute' => route('provider.edit.identity')
                        ])
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


