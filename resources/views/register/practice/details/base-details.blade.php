@extends('layouts.main')

@section('content')
    <div class="container" id="pushInPage">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        @include('register.practice.details.partials._forms-base', [
                            'showLinks' => true,
                            'photoUploadUrl' => route('practice.details.savePhoto'),
                            'formAction' => route('practice.details.practiceSaveDetails')
                        ])
                    </div>
                </div>

                <div class="text-center">
                    @if($user->isAccountActive())
                        <form method="post" action="{{ route('deactivate-account', $user) }}">
                            @csrf
                            <button type="submit" class="btn-link mt-2">Deactivate Account</button>
                        </form>
                    @else
                        <form method="post" action="{{ route('activate-account', $user) }}">
                            @csrf
                            <button type="submit" class="btn-link mt-2">Activate Account</button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
