@if ($user->isSuperAdmin())
    <span class="badge badge-danger">Super Admin</span>
@endif
@if ($user->isAdmin())
    <span class="badge badge-warning">Admin</span>
@endif
@if ($user->isProvider())
    <span class="badge badge-primary">Provider</span>
@endif
@if ($user->isPractice())
    <span class="badge badge-info">Practice</span>
@endif
@if ($user->isPartner())
    <span class="badge badge-warning">Partner</span>
@endif
