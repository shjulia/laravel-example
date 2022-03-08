@extends('layouts.main')

@section('content')
    <div class="container referral">
        <div class="row justify-content-center">
            <div class="col-md-9">
                <invites
                        reinvite-action="{{ route('referral.reinvite', ['invite' => '_invite_']) }}"
                        inline-template
                        v-cloak
                >
                    <div class="card">
                        <div class="card-header">
                            <h1>People Invited</h1>
                            <h2>Invite Friends & You Both Get Up To $100</h2>
                        </div>
                        <div class="card-body card-body-0">
                            <div class="table-responsive">
                                <table class="table">
                                    <thead class="thead-dark">
                                    <tr>
                                        <th scope="col">Name</th>
                                        <th scope="col">Email</th>
                                        <th scope="col">Invited Date</th>
                                        <th scope="col">Status</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($invites as $invite)
                                        <tr>
                                            <th class="name">
                                                @if($invite->user)
                                                    @if($invite->user->specialist->photo)
                                                        <img src="{{ $invite->user->specialist->photo_url }}" />
                                                    @endif
                                                    {{ $invite->user->full_name}}
                                                @endif
                                            </th>
                                            <td class="email">{{ $invite->email }}</td>
                                            <td class="date">{{ formatedTimestamp($invite->updated_at) }}</td>
                                            <td class="status-td">
                                                @if($invite->accepted)
                                                    <span class="status signed">Signed-up</span>
                                                @else
                                                    <span class=" status waiting">Waiting</span>
                                                    {{--<span><i class="fa fa-info info-c"></i></span>--}}
                                                    <button class="btn reinvite" @click="reinvite({{ $invite->id }})">Re Invite</button>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                            {{ $invites->links() }}
                        </div>
                    </div>
                </invites>
            </div>
        </div>
    </div>
@endsection
