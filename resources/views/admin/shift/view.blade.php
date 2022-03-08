@extends('layouts.main')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">Shift {{ $shift->id }}</div>
                    <div class="card-body">
                        <div class="d-flex flex-row mb-3">
                            @can('manage-shifts')
                                <div class="dropdown mr-1">
                                    <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        Edit
                                    </button>
                                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                        @if(Gate::check('can-edit-shift-admin', $shift))
                                            <a href="{{ route('admin.shifts.edit', $shift) }}" class="dropdown-item">Update</a>
                                        @endcan
                                        @if(Gate::check('edit-time-admin', $shift) && Gate::check('manage-shifts'))
                                            <a class="dropdown-item" href="{{ route('admin.shifts.edit.time', $shift) }}">Shift Time</a>
                                        @endif
                                    </div>
                                </div>
                            @endcan
                            @if ($shift->canBeCanceled() && Gate::check('manage-shifts'))
                                <form method="POST" action="{{ route('admin.shifts.cancel', $shift) }}" class="mr-1">
                                    @csrf
                                    <button class="btn btn-danger action-button-alert">Cancel shift</button>
                                </form>
                            @endif
                            @if ($shift->canRefund() && Gate::check('manage-shifts'))
                                <form method="POST" action="{{ route('admin.shifts.refund', $shift) }}" class="mr-1">
                                    @csrf
                                    <button class="btn btn-danger action-button-alert">Refund charge</button>
                                </form>
                            @endif
                            @if (($shift->isMatchingStatus() || $shift->isParentMatchingStatus()) && Gate::check('manage-shifts'))
                                <a href="{{ route('admin.shifts.invite', $shift) }}" class="btn btn-primary mb-2 mr-1">Invite</a>
                            @endif
                            @if (!$shift->isArchived() && Gate::check('manage-shifts'))
                                <form method="POST" action="{{ route('admin.shifts.archive', $shift) }}" class="mr-1">
                                    @csrf
                                    <button class="btn btn-danger action-button-alert">Archive</button>
                                </form>
                            @endif
                            @if ($shift->canBeReMatched() && Gate::check('manage-shifts'))
                                <form method="POST" action="{{ route('admin.shifts.restartMatching', $shift) }}" class="mr-1">
                                    @csrf
                                    <button class="btn btn-warning action-button-alert">Restart matching</button>
                                </form>
                            @endif
                            <a href="{{ route('admin.shifts.log', $shift) }}" class="btn btn-success mb-2 mr-1">Log</a>
                            <a href="{{ !$shift->isArchived() ? route('admin.shifts.index') : route('admin.shifts.archived')}}" class="btn btn-success mb-2 mr-1">List</a>
                        </div>

                        <table class="table table-bordered table-striped">
                            <tbody>
                                <tr>
                                    <th>ID</th><td>{{ $shift->id }}</td>
                                </tr>
                                @if ($shift->isChild())
                                    <tr>
                                        <th>Parent shift</th><td><a href="{{ route('admin.shifts.show', $shift->parent_shift_id)  }}">{{ $shift->parent->id }}</a></td>
                                    </tr>
                                @endif
                                <tr>
                                    <th>Practice</th><td>{{ $shift->practice->practice_name }}</td>
                                </tr>
                                <tr>
                                    <th>Location</th>
                                    <td>
                                        <b>Practice name: </b>{{ $shift->practice_location->practiceName }} <br/>
                                        <b>Address: </b>{{ $shift->practice_location->fullAddress() }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>Position</th><td>{{ $shift->position->title ?? '' }}</td>
                                </tr>
                                <tr>
                                    <th>Provider</th><td>{{ $shift->providersName() }}</td>
                                </tr>
                                @if ($shift->creator_id)
                                    <tr>
                                        <th>Creator</th><td><a href="{{ route('admin.users.show', $shift->creator)  }}">{{ $shift->creator->full_name }}</a></td>
                                    </tr>
                                @endif
                                <tr>
                                    <th>Status</th><td>
                                        @include('admin.shift._status', $shift)
                                    </td>
                                </tr>
                                @if ($shift->isCanceledStatus())
                                    <tr>
                                        <th>Cancellation fee</th>
                                        <td>
                                            {{ $shift->cancellation_fee ? "$" . $shift->cancellation_fee : "---" }}
                                        </td>
                                    </tr>
                                    @if ($shift->cancellation_charge_id)
                                        <tr>
                                            <th>Cancellation stripe charge id</th>
                                            <td>
                                                {{ $shift->cancellation_charge_id }}
                                            </td>
                                        </tr>
                                    @endif
                                    <tr>
                                        <th>Cancellation reason</th>
                                        <td>
                                            {{ $shift->cancellation_reason }}
                                        </td>
                                    </tr>
                                @endif
                                <tr>
                                    <th>Datetime in creator timezone</th><td>{{ $shift->period() }}</td>
                                </tr>
                                <tr>
                                    <th>Lunch break</th><td>{{ $shift->lunch_break ? $shift->lunch_break : '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Tasks</th><td>{{ $shift->tasks_names }}</td>
                                </tr>
                                {{--<tr>
                                    <th>Arrival time</th><td>{{ $shift->arrival_time }}</td>
                                </tr>--}}
                                <tr>
                                    <th>Cost</th><td>
                                        ${{ $shift->cost_without_surge . ($shift->surge_price ? ' + surge $' . $shift->surge_price : '' ) . ($shift->bonus  ? (' + bonus $' . $shift->bonus) : '') }}
                                        @can('edit-bonus-admin', $shift)
                                            <form action="{{ route('admin.shifts.edit.bonus', $shift) }}" method="POST" class="form-inline ml-1">
                                                @csrf
                                                <div class="form-group">
                                                    <label for="bonus">Bonus ($)</label>
                                                    <input type="text" class="form-control" value="{{ $shift->bonus }}" id="bonus" name="bonus" />
                                                </div>
                                                <button class="btn btn-info ml-1">{{ $shift->bonus ? 'Change bonus' : 'Add bonus' }}</button>
                                            </form>
                                        @endcan
                                    </td>
                                </tr>
                                <tr>
                                    <th>Cost for practice</th><td>${{ $shift->cost_for_practice }}</td>
                                </tr>
                                @if ($shift->isHasProvider() && $shift->provider->debt)
                                    <tr>
                                        <th>Provider total debt</th><td>${{ $shift->provider->debt }}</td>
                                    </tr>
                                @endif
                                @if ($shift->coupon_id)
                                    <tr>
                                        <th>Coupon</th><td><a href="{{ route('admin.coupons.show', $shift->coupon_id)  }}">coupon</a></td>
                                    </tr>
                                @endif
                                <tr>
                                    <th>Payment status</th><td>{{ $shift->paymentStatusString() }}</td>
                                </tr>
                                <tr>
                                    <th>Created At</th><td>{{ formatedTimestamp($shift->created_at) }}</td>
                                </tr>
                            </tbody>
                        </table>

                        <tracking-map
                            :shift="{{ $shift }}"
                            :tracks="{{ $shift->shiftTracking }}"
                            tz="{{ $shift->creator->tz }}"
                        >

                        </tracking-map>

                        @include('admin.shift.view-partials._practice-charges', ['shift' => $shift])

                        @if ($shift->multi_days)
                            <ul class="nav nav-tabs" id="dayTab" role="tablist">
                                @foreach($shift->children as $key => $child)
                                    <li class="nav-item">
                                        <a class="nav-link" id="d{{ $key }}-tab" data-toggle="tab" href="#d{{ $key }}" role="tab" aria-controls="d{{ $key }}" aria-selected="true">Day {{ $key + 1 }}</a>
                                    </li>
                                @endforeach
                            </ul>
                            <div class="tab-content bordered" id="dayTabContent">
                                @foreach($shift->children->each->setAppends(['practice_location']) as $key => $child)
                                    <div class="tab-pane fade" id="d{{ $key }}" role="tabpanel" aria-labelledby="d{{ $key }}-tab">
                                        <div class="d-flex flex-row mb-3 mt-3">
                                            @can('manage-shifts')
                                                <div class="dropdown mr-1">
                                                    <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                        Edit
                                                    </button>
                                                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                        @if(Gate::check('can-edit-shift-admin', $child) )
                                                            <a href="{{ route('admin.shifts.edit', $child) }}" class="dropdown-item">Update</a>
                                                        @endcan
                                                        @if(Gate::check('edit-time-admin', $child) && Gate::check('manage-shifts'))
                                                            <a class="dropdown-item" href="{{ route('admin.shifts.edit.time', $child) }}">Shift Time</a>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endcan
                                            @if ($child->canRefund() && Gate::check('manage-shifts'))
                                                <form method="POST" action="{{ route('admin.shifts.refund', $child) }}" class="mr-1">
                                                    @csrf
                                                    <button class="btn btn-danger action-button-alert">Refund charge</button>
                                                </form>
                                            @endif
                                            @if ($child->canBeCanceled() && Gate::check('manage-shifts'))
                                                <form method="POST" action="{{ route('admin.shifts.cancel', $child) }}" class="mr-1">
                                                    @csrf
                                                    <button class="btn btn-danger action-button-alert">Cancel shift</button>
                                                </form>
                                            @endif
                                            @if ($child->canBeReMatched() && Gate::check('manage-shifts'))
                                                <form method="POST" action="{{ route('admin.shifts.restartMatching', $child) }}" class="mr-1">
                                                    @csrf
                                                    <button class="btn btn-warning action-button-alert">Restart matching</button>
                                                </form>
                                            @endif
                                                <a href="{{ route('admin.shifts.show', $child)}}" class="btn btn-success mb-2 mr-1">View</a>
                                        </div>

                                        <table class="table table-bordered table-striped">
                                            <tbody>
                                            <tr><th>ID</th><td>{{ $child->id }}</td></tr>
                                            <tr><th>Provider</th><td>{{ $child->providersName() }}</td></tr>
                                            <tr><th>Status</th><td>@include('admin.shift._status', ['shift' => $child])</td></tr>
                                            @if ($child->isCanceledStatus())
                                                <tr><th>Cancellation fee</th><td>{{ $child->cancellation_fee ? "$" . $child->cancellation_fee : "---" }}</td></tr>
                                                <tr><th>Cancellation reason</th><td>{{ $child->cancellation_reason }}</td></tr>
                                            @endif
                                            <tr><th>Datetime in creator timezone</th><td>{{ $child->period() }}</td></tr>
                                            <tr><th>Lunch break</th><td>{{ $child->lunch_break ? $child->lunch_break : '-' }}</td></tr>
                                            <tr>
                                                <th>Cost</th><td>${{ $child->cost_without_surge . ($child->surge_price ? ' + surge $' . $child->surge_price : '' ) . ($child->bonus  ? (' + bonus $' . $child->bonus) : '') }}</td>
                                            </tr>
                                            <tr><th>Cost for practice</th><td>${{ $child->cost_for_practice }}</td></tr>
                                            @if ($child->coupon_id)
                                                <tr><th>Coupon</th><td><a href="{{ route('admin.coupons.show', $child->coupon_id)  }}">coupon</a></td></tr>
                                            @endif
                                            @if ($child->isHasProvider() && $child->provider->debt)
                                                <tr><th>Provider total debt</th><td>${{ $child->provider->debt }}</td></tr>
                                            @endif
                                            </tbody>
                                        </table>
                                        <tracking-map
                                            :shift="{{ $child }}"
                                            :tracks="{{ $child->shiftTracking }}"
                                            tz="{{ $child->creator->tz }}"
                                        >

                                        </tracking-map>
                                        @include('admin.shift.view-partials._practice-charges', ['shift' => $child])
                                        @include('admin.shift.view-partials._reviews', ['shift' => $child])
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        @if (!$shift->shiftInvites->isEmpty())
                            <div class="card mb-2">
                                <div class="card-header">Shift invites</div>
                                <div class="card-body">
                                    <table class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>Provider</th>
                                                <th>Status</th>
                                                <th>Created at</th>
                                            </tr>
                                        </thead>
                                        <tbody>

                                        @foreach ($shift->shiftInvites as $invite)
                                            <tr>
                                                <td><a href="{{ route('admin.users.show', $invite->provider) }}">{{ $invite->provider->user->full_name }}</a></td>
                                                <td>
                                                    @if ($invite->isNoRespond())
                                                        <span class="badge badge-primary">Invite Sent</span>
                                                    @elseif ($invite->isViewed())
                                                        <span class="badge badge-warning">Invite Viewed, No Response</span>
                                                    @elseif ($invite->isAccepted())
                                                        <span class="badge badge-success">Matched</span>
                                                    @elseif ($invite->isDeclined())
                                                        <span class="badge badge-danger">Declined</span>
                                                    @endif
                                                </td>
                                                <td>{{ formatedTimestamp($invite->created_at) }}</td>
                                            </tr>
                                        @endforeach

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endif

                        @include('admin.shift.view-partials._reviews')

                        @include('admin.shift.view-partials._matching-iterations')
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
