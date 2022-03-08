@extends('layouts.main')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card">
                    <div class="card-header">User {{ $user->full_name }} emails log</div>
                    <div class="card-body">
                        <a href="{{ route('admin.users.show', $user) }}" class="btn btn-success mr-1" >User data</a>
                        <email-manager
                            user-emails="{{ $emails->getCollection() }}"
                            tz="{{ $tz }}"
                            v-cloak
                            inline-template
                        >
                            <table class="table table-bordered table-striped">
                                <thead>
                                <tr>
                                    <th>User email</th>
                                    <th>Type</th>
                                    <th>Subject</th>
                                    <th>Email/Phone</th>
                                    <th>Created At</th>
                                    <th>Status</th>
                                    <th></th>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-for="email in emails">
                                    <td>@{{ email.user.email }}</td>
                                    <td>@{{ email.class }}</td>
                                    <td>@{{ email.subject }}</td>
                                    <td>@{{ email.contact }}</td>
                                    <td>@{{ formatedTime(email.updated_at) }}</td>
                                    <td>
                                        <span class="badge"
                                              :class="{'badge-secondary': email.last_status == 'accepted',
                                              'badge-primary': email.last_status == 'delivered',
                                              'badge-success': email.last_status == 'opened'}">@{{ email.last_status }}</span>
                                    </td>
                                    <td>
                                        <button class="btn btn-primary" @click="resend(email.id)">Resend</button>
                                    </td>
                                    <td>
                                        <button class="btn btn-secondary" @click="show(email)">Show</button>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </email-manager>
                        {{ $emails->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
