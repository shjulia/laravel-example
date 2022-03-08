@extends('layouts.main')

@section('content')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">Analytics</div>
                    <div class="card-body" v-cloak>

                        <div class="row">
                            <div class="col-md-6">
                                <total-number
                                        url="{{ route('admin.analytics.total-number') }}">
                                </total-number>
                            </div>
                            <div class="col-md-6">
                                <profit
                                    url="{{ route('admin.analytics.profit') }}"
                                    month-url ={{ route('admin.analytics.profit-by-month') }}
                                    future-url={{ route('admin.analytics.future-by-month') }}
                                >
                                </profit>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="analytics_div">
                                    <ul class="nav nav-tabs" id="ppTab" role="tablist">
                                        <li class="nav-item">
                                            <a class="nav-link active" id="providers-tab" data-toggle="tab" href="#providers" role="tab" aria-controls="providers" aria-selected="true">Providers</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" id="money-tab" data-toggle="tab" href="#money" role="tab" aria-controls="money" aria-selected="false">Revenue</a>
                                        </li>
                                    </ul>
                                    <div class="tab-content dh" id="ppTabContent">
                                        <div class="tab-pane show active" id="providers" role="tabpanel" aria-labelledby="providers-tab">
                                            <providers-positions
                                                    url="{{ route('admin.analytics.providers') }}"
                                            >
                                            </providers-positions>
                                        </div>
                                        <div class="tab-pane fade" id="money" role="tabpanel" aria-labelledby="money-tab">
                                            <providers-revenue
                                                    url="{{ route('admin.analytics.revenue') }}"
                                            >
                                            </providers-revenue>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <ul class="nav nav-tabs" id="topTab" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link active" id="paid-tab" data-toggle="tab" href="#paid" role="tab" aria-controls="paid" aria-selected="true">Top paid</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="top-tab" data-toggle="tab" href="#top" role="tab" aria-controls="top" aria-selected="false">Top rated</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="lowest-tab" data-toggle="tab" href="#lowest" role="tab" aria-controls="lowest" aria-selected="false">Lowest rated</a>
                                    </li>
                                </ul>
                                <div class="tab-content dh" id="topTabContent">
                                    <div class="tab-pane show active" id="paid" role="tabpanel" aria-labelledby="paid-tab">
                                        <top-list
                                            url="{{ route('admin.analytics.top-list') }}"
                                        ></top-list>
                                    </div>
                                    <div class="tab-pane fade" id="top" role="tabpanel" aria-labelledby="top-tab">
                                        <top-list
                                            url="{{ route('admin.analytics.getRatedTopList', ['top' => true]) }}"
                                        ></top-list>
                                    </div>
                                    <div class="tab-pane fade" id="lowest" role="tabpanel" aria-labelledby="lowest-tab">
                                        <top-list
                                            url="{{ route('admin.analytics.getRatedTopList', ['top' => 0]) }}"
                                        ></top-list>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="analytics_div">
                                    <ul class="nav nav-tabs" id="wTab" role="tablist">
                                        <li class="nav-item">
                                            <a class="nav-link active" id="worked-tab" data-toggle="tab" href="#worked" role="tab" aria-controls="worked" aria-selected="true">Total hours worked</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" id="day-tab" data-toggle="tab" href="#day" role="tab" aria-controls="day" aria-selected="false">Hours worked per day</a>
                                        </li>
                                    </ul>
                                    <div class="tab-content dh" id="wTabContent">
                                        <div class="tab-pane show active" id="worked" role="tabpanel" aria-labelledby="worked-tab">
                                            <div class="row mt-2">
                                                <div class="col">
                                                    <worked
                                                        url="{{ route('admin.analytics.totalWorked') }}"
                                                        title="Total hours worked and completed"
                                                        unit="hours"
                                                    >
                                                    </worked>
                                                </div>
                                                <div class="col">
                                                    <worked
                                                        url="{{ route('admin.analytics.avgMatchingTimeDay') }}"
                                                        title="Average time to match"
                                                        unit="minutes"
                                                    >
                                                    </worked>
                                                </div>
                                                <div class="col">
                                                    <worked
                                                        url="{{ route('admin.analytics.successPercent') }}"
                                                        title="% of successful matches"
                                                        unit="%"
                                                    >
                                                    </worked>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="tab-pane fade" id="day" role="tabpanel" aria-labelledby="day-tab">
                                            <worked-per-day
                                                    url="{{ route('admin.analytics.totalWorkedPerDay') }}"
                                                    :positions="{{ $positions }}"
                                            >
                                            </worked-per-day>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <cancell-anlytics
                                    pie-reason-url="{{ route('admin.analytics.cancellationReasons') }}"
                                ></cancell-anlytics>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="analytics_div">
                                    <div class="row">
                                        <div class="col">
                                            <worked
                                                url="{{ route('admin.analytics.approvalTime') }}"
                                                title="Time to Approval"
                                                unit="hours"
                                                view-url="{{ route('admin.analytics.approvalTimeDetails') }}"
                                            >
                                            </worked>
                                        </div>
                                        <div class="col">
                                            <worked
                                                url="{{ route('admin.analytics.completeTime') }}"
                                                title="Time to Complete Application"
                                                unit="hours"
                                                view-url="{{ route('admin.analytics.completeTimeDetails') }}"
                                            >
                                            </worked>
                                        </div>
                                        <div class="col">
                                            <worked
                                                url="{{ route('admin.analytics.findRejectedToApprovedRatio') }}"
                                                title="Rejected to Approved ratio"
                                                unit="%"
                                            >
                                            </worked>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
