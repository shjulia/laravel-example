<provider-edit
    check-url="{{ route('admin.users.check-approve', $user) }}"
    inline-template
>
    <div>
        @can('manage-users')
            <div class="mb-1">
                @if ($user->specialist->isWaiting())
                    <form method="POST" action="{{ route('admin.users.approve-provider', $user) }}" ref="approveform" class="mr-1">
                        @csrf
                        <input type="hidden" v-model="approval_reason" name="approval_reason" />
                        <button type="submit" @click.prevent.stop="approve()" class="btn btn-success">Approve provider</button>
                    </form>
                @else
                    <form method="POST" action="{{ route('admin.users.approve-provider', $user) }}" class="mr-1">
                        @csrf
                        <button class="btn btn-warning action-button-alert">Disapprove provider</button>
                    </form>
                @endif
            </div>
        @endcan
        <h3>Wallet</h3>
        <table class="table table-bordered table-striped">
            <tbody>
            @if($user->wallet)
            <tr>
                <th>Wallet ID</th>
                <td>
                    <a target="_blank" href="{{ walletClientUrl($user) }}">{{ $user->wallet->wallet_client_id }}</a>
                </td>
            </tr>
            <tr>
                <th>Has transfer data</th><td>{{ $user->wallet->has_transfer_data ? 'yes' : 'no' }}</td>
            </tr>
            @endif
            </tbody>
        </table>
        <h3>Provider data</h3>
        <table class="table table-bordered table-striped">
            <tbody>
            <tr>
                <th>Industry</th><td>{{ $user->specialist->industry->title ?? '' }}</td>
            </tr>
            <tr>
                <th>Position</th><td>{{ $user->specialist->position->title ?? '' }}</td>
            </tr>
            @if ($user->specialist->photo)
                <tr>
                    <th>Photo</th><td><img width="200px" src="{{ $user->specialist->photo_url }}" class="img-thumbnail"></td>
                </tr>
            @endif
            <tr>
                <th>SSN</th><td>{{ $user->specialist->ssn }}</td>
            </tr>
            <tr>
                <th>Specialities</th>
                <td>
                    @foreach($user->specialist->specialities as $speciality)
                        <span class="badge badge-light">{{ $speciality->title }}</span>
                    @endforeach
                </td>
            </tr>
            <tr>
                <th>Available</th>
                <td>
                    {{ $user->specialist->available ? 'yes' : 'no' }}
                    <form action="{{ route('admin.users.edit.changeAvailability', $user) }}" method="POST">
                        @csrf
                        <button class="btn btn-info" type="submit" @click="$loading.show()">Change</button>
                    </form>
                </td>
            </tr>
            <tr>
                <th>Availabilities</th>
                <td>
                    <ul>
                        @foreach($user->specialist->additional->availabilities as $av)
                            <li>
                                <b>Days:</b>
                                @foreach($av['inDays'] as $day)
                                    {{ $days[$day] }};
                                @endforeach
                                <b>From time: </b>{{ $av['from'] }}h. <b>To time: </b>{{ $av['to'] }}h.
                            </li>
                        @endforeach
                    </ul>
                </td>
            </tr>
            <tr>
                <th>Holiday Availabilities</th>
                <td>
                    <ul>
                        @foreach($user->specialist->holidays as $holiday)
                            <li>{{ $holiday->title  }}</li>
                        @endforeach
                    </ul>
                </td>
            </tr>
            <tr>
                <th>Min Rate</th>
                <td>{{ $user->specialist->min_rate ? ('$' . $user->specialist->min_rate) : 'not set' }}</td>
            </tr>
            </tbody>
        </table>
        <h3>Medical licenses</h3>
        <table class="table table-bordered table-striped">
            <tbody>
            @foreach($user->specialist->licenses as $license)
                <tr>
                    <th>{{ $license->type }}</th>
                    <td>
                        <ul>
                            @if($license->photo)
                                <li><img width="200px" src="{{ $license->photo_url }}" class="img-thumbnail"></li>
                            @endif
                            <li><b>Number: </b>{{ $license->number }}</li>
                            <li>
                                <b>Expiration date: </b> {{ $license->expiration_date ? $license->expiration_date->format('Y-m-d') : '' }}
                                @if ($license->isDateExpired())
                                    <span class="badge badge-warning">Expired</span>
                                @endif
                            </li>
                            <li><b>State: </b>{{ $license->state }}</li>
                            <li>
                                @if ($license->isBaseStatus())
                                    <span class="badge badge-secondary">Status not set</span>
                                @elseif ($license->isApproved())
                                    <span class="badge badge-success">Approved</span>
                                @elseif ($license->isDeclined())
                                    <span class="badge badge-danger">Declined</span>
                                    @if($license->declined_reason )
                                        <p><b>Reason: </b>{{ $license->declined_reason }}</p>
                                    @endif
                                @endif
                            </li>
                        </ul>
                        <div class="dropdown">
                            <button class="btn btn-secondary btn-sm dropdown-toggle" type="button" id="actionsDDMB" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                Actions
                            </button>
                            <div class="dropdown-menu" aria-labelledby="actionsDDMB">
                                @if (!$license->isApproved())
                                    <form action="{{ route('admin.users.provider.license.approve', $license) }}" method="post">
                                        @csrf
                                        <button type="submit" class="dropdown-item">Approve</button>
                                    </form>
                                @endif
                                @if (!$license->isDeclined())
                                    <a class="dropdown-item" href.prevent="#" @click="declineModal('{{ route('admin.users.provider.license.decline', $license) }}')">Decline</a>
                                @endif
                                @if (!$license->isBaseStatus())
                                    <form action="{{ route('admin.users.provider.license.setBase', $license) }}" method="post">
                                        @csrf
                                        <button type="submit" class="dropdown-item">Set Base</button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
        <div class="modal fade" id="declineModal" tabindex="-1" role="dialog" aria-labelledby="declineModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form :action="declineAction" method="post">
                            @csrf
                            <div class="form-group">
                                <label>Reason</label>
                                <textarea class="form-control" name="reason"></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary">Decline</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <h3>Driver license data</h3>
        <table class="table table-bordered table-striped table-responsive">
            <tbody>
            <tr>
                <th>Compare photos</th>
                <td>
                    @if ($user->specialist->driver_photo && $user->specialist->photo)
                        <form method="POST" action="{{ route('admin.users.compare', $user) }}" class="mr-1" @submit="$loading.show()">
                            @csrf
                            <button class="btn btn-success action-button-alert">Compare photos</button>
                        </form>
                    @endif
                    @if(!is_null($user->specialist->photos_similar))
                        <span><b>Faces similar persent: </b>{{ $user->specialist->photos_similar }} %</span>
                    @endif
                </td>
            </tr>
            <tr>
                <th>Name</th><td>{{ $user->specialist->driver_first_name .' ' .$user->specialist->driver_middle_name .' ' .$user->specialist->driver_last_name }}</td>
            </tr>
            @if ($user->specialist->driver_photo)
                <tr>
                    <th>License photo</th><td><img width="200px" src="{{ $user->specialist->driver_photo_url }}" class="img-thumbnail"></td>
                </tr>
            @endif
            <tr>
                <th>Address</th><td>{!! $user->specialist->driver_address . '<br/> ' . $user->specialist->driver_city . ', ' . $user->specialist->driver_state . ' ' . $user->specialist->driver_zip !!}</td>
            </tr>
            <tr>
                <th>Date of birth</th><td>{{ $user->specialist->dob }}</td>
            </tr>
            <tr>
                <th>Expiration date</th><td>{{ $user->specialist->driver_expiration_date ? $user->specialist->driver_expiration_date->format('Y-m-d') : '' }}</td>
            </tr>
            <tr>
                <th>Gender</th><td>{{ $user->specialist->driver_gender }}</td>
            </tr>
            <tr>
                <th>License number</th><td>{{ $user->specialist->driver_license_number }}</td>
            </tr>
            @if($user->specialist->checkr)
                <tr>
                    <th>Checkr status</th><td>
                    <span
                        class="badge @if($user->specialist->checkr->isClear()) {{ 'badge-success' }} @elseif($user->specialist->checkr->isConsider()) {{ 'badge-danger' }} @else {{ 'badge-secondary' }} @endif"
                    >
                        {{ $user->specialist->checkr->checkr_status }}
                    </span>
                    </td>
                </tr>
                @if ($user->specialist->checkr->checkr_candidate_id)
                    <tr>
                        <th>Checkr candidate id</th><td>{{ $user->specialist->checkr->checkr_candidate_id }}</td>
                    </tr>
                @endif
                @if ($user->specialist->checkr->checkr_report_id)
                    <tr>
                        <th>Checkr report id</th><td>{{ $user->specialist->checkr->checkr_report_id }}</td>
                    </tr>
                @endif
            @endif

            </tbody>
        </table>
        <h3>Work history</h3>
        <table class="table table-bordered table-striped">
            <tbody>
                <tr>
                    <th>Reviews total</th><td>{{ $user->specialist->reviews_total }}</td>
                </tr>
                <tr>
                    <th>Average rating</th><td>{{ $user->specialist->average_stars }} / 5</td>
                </tr>
                <tr>
                    <th>Reviews total to practices</th><td>{{ $user->specialist->reviews_to_practice_total }}</td>
                </tr>
                <tr>
                    <th>Average rating to practices</th><td>{{ $user->specialist->average_stars_to_practice }} / 5</td>
                </tr>
            </tbody>
        </table>
    </div>
</provider-edit>
