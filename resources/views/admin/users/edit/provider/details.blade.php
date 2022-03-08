@extends('admin.users.edit.edit')
@section('edit-content')
    @include('register.provider.details._forms', [
        'uploadAvatarUrl' => route('admin.users.edit.avatar', $user),
        'formAction' => route('admin.users.edit.details', $user)
    ])
@endsection
