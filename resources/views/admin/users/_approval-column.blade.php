<div class="card text-white {{ $bg }} mb-3">
    <div class="card-header">{{ $columnName }} Column</div>
    <div class="card-body">
        @foreach($list as $user)
            <div class="card text-center adm-users-card">
                <div class="card-body">
                    <h5 class="card-title mb-0"><a href="{{ route('admin.users.show', $user) }}">{{ $user->id . '. ' . $user->full_name }}</a></h5>
                    <p>
                        @include('admin.users._show-role')
                    </p>
                    <h6 class="card-subtitle mb-2 text-muted">Step: <b>{{ $user->signup_step ?: 'signup finished' }}</b></h6>
                    <h6 class="card-subtitle text-muted">Last signup action: <b>{{ $user->last_signup_action_date }}</b></h6>
                </div>
            </div>
        @endforeach
    </div>
</div>
