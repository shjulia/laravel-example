@extends('admin.users.edit.edit')
@section('edit-content')
    @include('license._form', [
        'uploadPhotoUrl' => route('admin.users.edit.uploadMedical', $user),
        'action' => route('admin.users.edit.licenses', $user),
        'removeLicenseUrl' => route('admin.users.edit.removeLicense', $user),
        'saveOneAction' => route('admin.users.edit.oneLicense', $user)
    ])
@endsection
