@extends('layouts.main')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card">
                    <div class="card-header">Edit user {{ $user->id }}</div>
                    <div class="card-body">
                        <a href="{{ route('admin.users.show', $user) }}" class="btn btn-primary btn-sm">User show</a> <br/>
                        <ul class="nav nav-tabs" id="myTab" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link @if($tab == "userData") active @endif" href="{{ route('admin.users.edit.userData', $user) }}" role="tab">User data</a>
                            </li>
                            @if ($user->isProvider())
                                <li class="nav-item">
                                    <a class="nav-link @if($tab == "position") active @endif" href="{{ route('admin.users.edit.position', $user) }}">Provider position</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link @if($tab == "licenses") active @endif" href="{{ route('admin.users.edit.licenses', $user) }}" role="tab">Provider licenses</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link @if($tab == "check") active @endif" href="{{ route('admin.users.edit.check', $user) }}" role="tab">Provider check</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link @if($tab == "details") active @endif" href="{{ route('admin.users.edit.details', $user) }}" role="tab">Provider details</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link @if($tab == "rate") active @endif" href="{{ route('admin.users.edit.rate', $user) }}" role="tab">Provider rate</a>
                                </li>
                            @endif
                            @if ($user->isPractice())
                                <li class="nav-item">
                                    <a class="nav-link @if($tab == "practice-base") active @endif" href="{{ route('admin.users.edit.base', $user) }}" role="tab">Practice base info</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link @if($tab == "insurance") active @endif" href="{{ route('admin.users.edit.insurance', $user) }}" role="tab">Insurance</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link @if($tab == "base-details") active @endif" href="{{ route('admin.users.edit.details.base', $user) }}" role="tab">Details base</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link @if($tab == "secondary-details") active @endif" href="{{ route('admin.users.edit.details.secondary', $user) }}" role="tab">Details secondary</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link @if($tab == "team") active @endif" href="{{ route('admin.users.edit.details.team', $user) }}" role="tab">Team</a>
                                </li>
                            @endif
                        </ul>
                        <p></p>
                        @yield('edit-content')
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
