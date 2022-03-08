@extends('layouts.main')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card">
                    <div class="card-header">Mailing</div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('admin.mailing.referralInfo') }}" class="mr-1">
                            @csrf
                            <button class="btn btn-danger delete-button-alert">Send marketing referral email</button>
                        </form>
                        <form method="POST" action="{{ route('admin.mailing.referralInfo', ['test' => 1]) }}" class="mr-1 mt-3">
                            @csrf
                            <button class="btn btn-danger delete-button-alert">Send marketing referral email test</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
