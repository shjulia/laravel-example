@extends('admin.users.edit.edit')
@section('edit-content')
    @include('register.practice.details.partials._form-secondary', [
        'fromAction' => route('admin.users.edit.details.secondary', $user),
        'showLinks' => false,
    ])
@endsection
