@if ($shift->isCreatingStatus())
    <span class="badge badge-secondary">{{ $shift->status }}</span>
@elseif ($shift->isWaitingStatus())
    <span class="badge badge-dark">{{ $shift->status }}</span>
@elseif ($shift->isMatchingStatus())
    <span class="badge badge-primary">{{ $shift->status }}</span>
@elseif ($shift->isParentMatchingStatus())
    <span class="badge badge-primary">{{ $shift->status }}</span>
@elseif ($shift->isFinishedStatus())
    <span class="badge badge-success">{{ $shift->status }}</span>
@elseif ($shift->isCompleted())
    <span class="badge badge-dark">completed</span>
@elseif ($shift->isAcceptedByProviderStatus())
    <span class="badge badge-info">{{ $shift->status }}</span>
@elseif ($shift->isCanceledByPracticeStatus())
    <span class="badge badge-warning">{{ $shift->status }}</span>
@elseif ($shift->isCanceledStatus())
    <span class="badge badge-warning">{{ $shift->status }}</span>
@elseif ($shift->isArchived())
    <span class="badge badge-danger">{{ $shift->status }}</span>
@elseif ($shift->isNoPrividerFoundStatus())
    <span class="badge badge-danger">{{ $shift->status }}</span>
@endif
