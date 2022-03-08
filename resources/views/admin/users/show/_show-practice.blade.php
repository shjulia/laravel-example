<div>
    @can('manage-users')
        <div class="mb-1">
            @if ($user->practice->isWaiting())
                <form method="POST" action="{{ route('admin.users.approve-practice', $user) }}" class="mr-1">
                    @csrf
                    <button class="btn btn-success action-button-alert">Approve practice</button>
                </form>
            @else
                <form method="POST" action="{{ route('admin.users.approve-practice', $user) }}" class="mr-1">
                    @csrf
                    <button class="btn btn-warning action-button-alert">Disapprove practice</button>
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
                <th>Has payment data</th><td>{{ $user->wallet->has_payment_data ? 'yes' : 'no' }}</td>
            </tr>
        @endif
        </tbody>
    </table>
    <h3>Practice {{ $practice->id }} data </h3>
    <table class="table table-bordered table-striped">
        <tbody>
        <tr>
            <th>Industry</th><td>{{ $practice->industry->title }}</td>
        </tr>
        <tr>
            <th>Practice name</th><td>{{ explode(",", $practice->practice_name)[0] }}</td>
        </tr>
        <tr>
            <th>Rate</th>
            <td>
                <span class="badge badge-primary">{{ $practice->rate ? $practice->rate->title : 'Default' }}</span>
                <form method="POST" action="{{ route('admin.users.edit.addRate', $user) }}" class="mr-1 form-inline">
                    @csrf
                    <div class="form-group">
                        <label for="rate">Change rate</label>
                        <select name="rate" class="form-control" id="rate">
                            <option value="">default</option>
                            @foreach($rates as $rate)
                                <option value="{{ $rate->id }}" @if($practice->rate_id == $rate->id) selected @endif>{{ $rate->title }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button class="btn btn-primary action-button-alert ml-2">Change Rate</button>
                </form>
            </td>
        </tr>
        <tr>
            <th>Address</th><td>{!! $practice->address . '<br/> ' . $practice->city . ', ' . $practice->state . ' ' . $practice->zip !!}</td>
        </tr>
        <tr>
            <th>Practice URL</th><td><a href="{{ $practice->url }}">{{ $practice->url }}</a></td>
        </tr>
        <tr>
            <th>Practice phone</th><td>{{ $practice->practice_phone }}</td>
        </tr>
        @if ($practice->policy_photo)
            <tr>
                <th>Policy Photo</th><td><img width="200px" src="{{ $practice->policy_photo_url }}" class="img-thumbnail"></td>
            </tr>
        @endif
        @if (!$practice->no_policy)
            <tr>
                <th>Policy Type</th><td>{{ $practice->policy_type }}</td>
            </tr>
            <tr>
                <th>Policy Number</th><td>{{ $practice->policy_number }}</td>
            </tr>
            <tr>
                <th>Policy Expiration date</th><td>{{ $practice->policy_expiration_date }}</td>
            </tr>
            <tr>
                <th>Policy Provider</th><td>{{ $practice->policy_provider }}</td>
            </tr>
        @else
            <tr>
                <th>No Policy</th><td>No Policy</td>
            </tr>
        @endif
        @if ($practice->practice_photo)
            <tr>
                <th>Practice Photo</th><td><img width="200px" src="{{ $practice->practice_photo_url }}" class="img-thumbnail"></td>
            </tr>
        @endif
        <tr>
            <th>Practice culture</th><td>{{ $practice->culture }}</td>
        </tr>
        <tr>
            <th>Special notes</th><td>{{ $practice->notes }}</td>
        </tr>
        <tr>
            <th>On-site Point of Contact</th><td>{{ $practice->on_site_contact }}</td>
        </tr>
        <tr>
            <th>Provider park</th><td>{{ $practice->park }}</td>
        </tr>
        <tr>
            <th>Door for provider</th><td>{{ $practice->door }}</td>
        </tr>
        <tr>
            <th>Dress code policy</th><td>{{ $practice->dress_code }}</td>
        </tr>
        <tr>
            <th>Additional info</th><td>{{ $practice->info }}</td>
        </tr>
        <tr>
            <th>Stripe client id</th><td>{{ $practice->stripe_client_id }}</td>
        </tr>
        </tbody>
    </table>
    <h3>Work history</h3>
    <table class="table table-bordered table-striped">
        <tbody>
        <tr>
            <th>Reviews total</th><td>{{ $practice->reviews_total }}</td>
        </tr>
        <tr>
            <th>Average rating</th><td>{{ $practice->average_stars }} / 5</td>
        </tr>
        <tr>
            <th>Reviews to providers total</th><td>{{ $practice->reviews_to_provider_total }}</td>
        </tr>
        <tr>
            <th>Average rating to providers</th><td>{{ $practice->average_stars_to_provider }} / 5</td>
        </tr>
        </tbody>
    </table>
    <div class="row">
        <div class="col-md-12">
            <h3>Practice team</h3>
        </div>
        @foreach($practice->users as $subUser)
            @if ($subUser->id == $user->id)
                @continue
            @endif
            <div class="col-6">
                <div class="d-flex flex-row mb-3">
                    <a href="{{ route('admin.users.edit.userData', $subUser) }}" class="btn btn-primary mr-1" >Edit</a>

                    <form method="POST" action="{{ route('admin.users.destroy', $subUser) }}" class="mr-1 delete-user-form">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-danger delete-button-alert">Delete</button>
                    </form>

                    @if (!$subUser->isRejected())
                        <form method="POST" action="{{ route('admin.users.reject', $subUser) }}" class="mr-1 delete-user-form">
                            @csrf
                            <button class="btn btn-danger delete-button-alert">Reject</button>
                        </form>
                    @else
                        <form method="POST" action="{{ route('admin.users.un-reject', $user) }}" class="mr-1 delete-user-form">
                            @csrf
                            <button class="btn btn-warning delete-button-alert">Un-reject</button>
                        </form>
                    @endif

                    <a href="{{ route('admin.users.showEmails', $subUser) }}" class="btn btn-secondary mr-1">Emails log</a>
                    @can('login-as')
                        <form method="POST" action="{{ route('admin.users.login-as', $user) }}">
                            @csrf
                            <button type="submit" class="btn btn-primary">LoginAs</button>
                        </form>
                    @endcan
                </div>

                <table class="table table-bordered table-striped">
                    <tbody>
                    <tr>
                        <th>ID</th><td>{{ $subUser->id }}</td>
                    </tr>
                    <tr>
                        <th>Full name</th><td>{{ $subUser->full_name }}</td>
                    </tr>
                    <tr>
                        <th>Email</th><td>{{ $subUser->email }}</td>
                    </tr>
                    @if ($subUser->isRejected())
                        <tr>
                            <th>Rejected</th>
                            <td>
                                <span class="badge badge-danger">Rejected</span>
                            </td>
                        </tr>
                    @endif
                    <tr>
                        <th>Role in practice</th>
                        <td>
                            <span class="badge badge-primary">{{ $subUser->pivot->practice_role }}</span>
                        </td>
                    </tr>
                    <tr>
                        <th>Phone</th><td>{{ $subUser->phone }}</td>
                    </tr>
                    <tr>
                        <th>Created At</th><td>{{ formatedTimestamp($subUser->created_at) }}</td>
                    </tr>
                    </tbody>
                </table>
            </div>
        @endforeach
    </div>
</div>

