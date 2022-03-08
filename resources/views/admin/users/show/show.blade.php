@extends('layouts.main')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card">
                    <div class="card-header">Users</div>
                    <div class="card-body">
                        @include('admin.users.show._user-data')
                        @if ($user->isProvider())
                            @include('admin.users.show._show-provider')
                        @endif
                        @if ($user->isPractice())
                            @include('admin.users.show._show-practice', [
                                'practice' => $user->practice
                            ])
                        @endif
                        @if ($user->referral)
                            @include('admin.users.show._show-partner')
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
