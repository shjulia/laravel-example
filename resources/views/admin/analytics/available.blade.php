@extends('layouts.main')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <br/>
                <available
                    :data="{{ $data }}"
                    :states="{{ $states }}"
                    state-init="{{ $state ?: 'all' }}"
                    base-url="{{ route('admin.analytics.available') }}"
                >
                </available>
            </div>
        </div>
    </div>
@endsection
