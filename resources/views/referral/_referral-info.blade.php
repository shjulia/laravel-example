<referral
    invite-action="{{ route('referral.invite') }}"
    :invites-count-init="{{ $invitesCount }}"
    :referred-amount="{{ $referral->referred_amount }}"
    code-init="{{ $referral->referral_code }}"
    base-invite-url="{{ route('signup.userBaseByInvite', ['code' => '']) }}"
    change-code-action="{{ route('referral.code') }}"
    inline-template
    v-cloak
>
    <div class="card">
        <div class="card-header">
            <h1>Invite Friends + Earn Cash</h1>
            <h2>Receive $100 for every new Practice or Provider that signs up and utilized the platform.</h2>
        </div>
        <div class="card-body">
            <p class="title">Your Custom Invite Link to Share</p>
            <div class="readinput" data-toggle="tooltip">
                <input @click="openChange()" id="link" readonly :value="inviteUrl" data-placement="left" title="Click to change link" />
                <button class="btn pull-right btn-copy" data-clipboard-target="#link" @click="copy()"><i class="fa fa-clone" aria-hidden="true"></i> @{{ copyButtonText }}</button>
            </div>
            <button class="btn mob-button-copy btn-copy" data-clipboard-target="#link" @click="copy()"><i class="fa fa-clone" aria-hidden="true"></i> @{{ copyButtonText }}</button>
            <social-sharing :url="inviteUrl"
                            title="Join to boon"
                            description="Connecting licensed professionals with opportunities to provide care for others by practicing good."
                            hashtags="boon,dental"
                            twitter-user="boon"
                            inline-template>
                <div class="sharelinks">
                    <network network="facebook">
                        <i class="fa fa-facebook"></i> Facebook
                    </network>
                    <network network="twitter">
                        <i class="fa fa-twitter"></i> Twitter
                    </network>
                    <network network="linkedin">
                        <i class="fa fa-linkedin"></i> LinkedIn
                    </network>
                    <span class="email" :class="$parent.showInvite ? 'active' : ''" @click="$parent.emailClick()">
                    <i class="fa fa-envelope-o"></i> Email
                </span>
                </div>
            </social-sharing>

            <div v-if="showInvite">
                <div class="inputwithbutton">
                    <input placeholder="Email" v-model="email" />
                    <button class="btn pull-right" @click="invite()"><i class="fa fa-envelope-o"></i> Invite</button>
                </div>
                <div class="invalid-feedback" v-if="server_errors.email">@{{ server_errors.email }}</div>
            </div>
            <div class="row infos">
                <div class="col-md-3">
                    <div class="colinfo">
                        <a href="{{ route('referral.invites') }}">
                            <p class="value">@{{ invitesCount }}</p>
                            <i class="fa fa-users" aria-hidden="true"></i>
                            <p class="title">Friends Invited</p>
                        </a>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="colinfo">
                        <p class="value">@{{ referredAmount }}</p>
                        <i class="fa fa-handshake-o" aria-hidden="true"></i>
                        <p class="title">Accepted Invites</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="colinfo">
                        <p class="value">@{{ invitesCount - referredAmount }}</p>
                        <i class="fa fa-user-times" aria-hidden="true"></i>
                        <p class="title">Awaiting Response</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="colinfo">
                        <p class="value">${{ $referral->referral_money_earned }}</p>
                        <i class="fa fa-money" aria-hidden="true"></i>
                        <p class="title">Cash Earned</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="changeModal" tabindex="-1" role="dialog" aria-labelledby="changeModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <label for="basic-url">Change referral URL</label>
                        <div class="input-group mb-3 inprep">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="basic-addon3">{{ route('signup.userBaseByInvite', ['code' => '']) }}/</span>
                            </div>
                            <input v-model="code" type="text" class="form-control" aria-describedby="basic-addon3">
                            <div class="input-group-append">
                                <button @click="changeCode()" class="btn btn-success" type="button">Change</button>
                            </div>
                        </div>
                        <div class="invalid-feedback" v-if="server_errors.code">@{{ server_errors.code }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</referral>
