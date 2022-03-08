@if (session('localsuccess'))
    <div class="alert alert-success">
        {{ session('localsuccess') }}
    </div>
@endif

@if (session('localerror'))
    <div class="alert alert-danger">
        {{ session('localerror') }}
    </div>
@endif