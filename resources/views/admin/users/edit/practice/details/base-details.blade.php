@extends('admin.users.edit.edit')
@section('edit-content')
    @include('register.practice.details.partials._forms-base', [
        'showLinks' => false,
        'photoUploadUrl' => route('admin.users.edit.details.savePhoto', $user),
        'formAction' => route('admin.users.edit.details.base', $user)
    ])
@endsection
