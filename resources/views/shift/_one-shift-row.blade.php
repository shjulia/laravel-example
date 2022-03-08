<div class="oneupcoming">
    <p class="date">
        {{ $shift->period() }}
        @if($shift->lunch_break)
            <span>({{ $shift->lunch_break }} min. lunch)</span>
        @endif
    </p>
    @if ($shift->isAcceptedByProviderStatus())
        @if ($shift->isHasProvider())
            <a href="{{ route('shifts.details', $shift) }}" class="person" @click="$loading.show()">
                {{ $shift->provider->user->first_name  . ' ' . $shift->provider->user->last_name . ' (' . $shift->position->title . ')' }}
            </a>
        @else
            <a href="{{ route('shifts.details', $shift) }}" class="person" @click="$loading.show()">
                Multi-day shift
            </a>
        @endif
    @else
        @if ($shift->isCreatingStatus())
            <a href="{{ route('shifts.base', $shift) }}" @click="$loading.show()" class="boon-link">Finish creating this shift request</a>
        @elseif ($shift->multi_days)
            <a href="{{ route('shifts.details', $shift) }}" @click="$loading.show()" class="boon-link">Multi-day shift</a>
        @elseif ($shift->isMatchingStatus() || $shift->isParentMatchingStatus())
            <a href="{{ route('shifts.result', $shift) }}" @click="$loading.show()" class="boon-link">Search in progress</a>
        @else
            <a href="{{ route('shifts.details', $shift) }}" @click="$loading.show()" class="boon-link">{{ $shift->statusName() }}</a>
        @endif
    @endif
    <hr />
</div>
