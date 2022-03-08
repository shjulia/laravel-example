@extends('admin.users.edit.edit')
@section('edit-content')
    @include('register.practice._base-form', [
        'action' => route('admin.users.edit.baseSave', $user)
    ])
@endsection
