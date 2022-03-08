@extends('layouts.main')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body">
                        <a href="{{ route('practice.details.billing') }}" @click="$loading.show()" class="back"><i class="fa fa-chevron-left"></i> BACK</a>
                        <div class="text-center">
                            <i class="fa fa-check-circle-o congrat-circle" aria-hidden="true"></i>
                            <h1>Congratulations!</h1>
                            @if ($user->isWait())
                                <p class="h1_subtitle">Your account is now pending approval. You will receive an email notification as soon as your account has been approved.</p>
                            @elseif ($user->practice->isSetPaymentInfo())
                                <p class="h1_subtitle">Now you're all set to hire!</p>
                            @else
                                <p class="h1_subtitle">You can hire after setting billing info</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
