@extends('layouts.main')

@section('content')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">Transactions</div>
                    <div class="card-body">
                        <ul class="nav nav-tabs" id="myTab" role="tablist">
                            <li class="nav-item {{ $tab === 'practices'  ? 'active' : '' }}">
                                <a class="nav-link {{ $tab === 'practices'  ? 'active show' : '' }}" id="practices-tab" href="{{ route('admin.analytics.transactions.practices') }}" >Practices transactions</a>
                            </li>
                        </ul>
                        <div class="tab-content" id="myTabContent">
                            <div class="tab-pane fade show active" id="{{ $tab }}" role="tabpanel" aria-labelledby="{{ $tab }}-tab">
                                @yield('cont')
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
