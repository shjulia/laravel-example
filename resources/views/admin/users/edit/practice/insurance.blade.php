@extends('admin.users.edit.edit')
@section('edit-content')
    @include('register.practice._insurance-form', [
        'action' => route('admin.users.edit.insuranceSave', $user),
        'uploadUrl' => route('admin.users.edit.uploadInsurance', $user),
        'removeUrl' => route('admin.users.edit.removeInsurance', $user)
    ])
@endsection
