
@extends('layouts.main')

@section('content')
<div class="container"  id="pushInPage">
    <div class="row justify-content-center">
        <div class="col-xl-6 col-12">
            <div class="card details">
                <div class="card-body">
                    <a href="{{ route('home') }}" class="back"><i class="fa fa-chevron-left"></i> BACK</a>
                    <h2 class="detailsh2">Update Your Profile</h2>
                    @include('register.provider.details._forms', [
                        'uploadAvatarUrl' => route('savePhoto'),
                        'formAction' => route('saveDetails')
                    ])
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


