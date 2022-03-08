@extends('layouts.main')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <a href="{{ route('practice.details.locations') }}" class="back" @click="$loading.show()"><i class="fa fa-chevron-left"></i> BACK</a>
                        <h2 class="detailsh2">Team Members</h2>
                        @include('register.practice.details.partials._form-team', [
                            'action' => route('practice.details.teamSave'),
                            'deleteAction' => route('practice.details.deleteMember'),
                        ])
                        <div class="form-group row">
                            <div class="col-6 col-sm-6">
                                <a href="{{ route('practice.details.billing') }}" @click="$loading.show()" class="btn form-button skip-button">Skip</a>
                            </div>
                            <div class="col-6 col-sm-6">
                                <a href="{{ route('practice.details.billing') }}" @click="$loading.show()" class="btn form-button">Continue</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
