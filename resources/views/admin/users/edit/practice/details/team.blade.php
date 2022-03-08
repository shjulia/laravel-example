@extends('admin.users.edit.edit')
@section('edit-content')
    @include('register.practice.details.partials._form-team', [
        'action' => route('admin.users.edit.details.team', $user),
        'deleteAction' => route('admin.users.edit.details.team', $user),
    ])
@endsection
